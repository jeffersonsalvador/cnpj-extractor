<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 07.12.23 00:15
 */

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Cnae;
use App\Models\Company;
use App\Models\Country;
use App\Models\Establishment;
use App\Models\LegalNature;
use App\Models\Partner;
use App\Models\PartnerQualification;
use App\Models\Simple;
use App\Services\CSVProcessingService;
use App\Services\ZipProcessingService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Helper\ProgressBar;
use ZipArchive;

class ProcessCNPJ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:cnpj';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa arquivos CNPJ em ZIP e insere no banco de dados.';

    /**
     * Class constructor.
     *
     * @param  ZipProcessingService  $zipService
     * @param  CSVProcessingService  $csvService
     */
    public function __construct(
        readonly ZipProcessingService $zipService,
        readonly CSVProcessingService $csvService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     * @throws InvalidArgument
     * @throws UnavailableStream
     */
    public function handle(): void
    {
        // Run individual test file
        $this->runTestFile(env('RUN_TEST_FILE', false));
        $zipFiles = $this->getZipFiles();
        $progressBar = new ProgressBar($this->output, count($zipFiles));

        foreach ($zipFiles as $zipFile) {
            try {
                $tempDir = $this->zipService->extract($zipFile);
                $progressBar->advance();

                $filename = $this->getFirstFileNameFromZip($zipFile);
                $model = $this->getModelForFile($filename);
                $this->info("\nProcessing file: $filename");
                $this->csvService->process($tempDir.'/'.$filename, $model, $this->output);

                $this->cleanUp($tempDir);
            } catch (Exception $e) {
                Log::error('Erro no processamento: ' . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->info("\nProcess finished!");
    }

    /**
     * @return array|false
     */
    private function getZipFiles()
    {
        $zipFilesDirectory = base_path(env('ZIP_FILES_DIRECTORY'));

        return glob($zipFilesDirectory . '/*.zip');
    }

    /**
     * Get the first file name from a ZIP file
     * @param string $zipFilePath
     * @return string
     * @throws Exception
     */
    private function getFirstFileNameFromZip($zipFilePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            $filename = $zip->getNameIndex(0);
            $zip->close();
            return $filename;
        } else {
            throw new Exception("Não foi possível abrir o arquivo ZIP: $zipFilePath");
        }
    }

    /**
     * Get the file type
     * @param string $type
     * @param string $default
     * @return string
     */
    private function getFileType(string $type, string $default): string
    {
        return env('FILE_TYPE_' . $type, ".{$default}");
    }

    /**
     * Get the model for a given file
     * @param string $filename
     * @return Model|null
     */
    private function getModelForFile(string $filename): ?Model
    {
        return match (true) {
            str_contains($filename, $this->getFileType('CITY', 'MUNICCSV')) => app(City::class),
            str_contains($filename, $this->getFileType('COUNTRY', 'PAISCSV')) => app(Country::class),
            str_contains($filename, $this->getFileType('CNAE', 'CNAECSV')) => app(Cnae::class),
            str_contains($filename, $this->getFileType('COMPANY', 'EMPRECSV')) => app(Company::class),
            str_contains($filename, $this->getFileType('ESTABLISHMENT', 'ESTABELE')) => app(Establishment::class),
            str_contains($filename, $this->getFileType('LEGAL_NATURE', 'NATJUCSV')) => app(LegalNature::class),
            str_contains($filename, $this->getFileType('PARTNER', 'SOCIOCSV')) => app(Partner::class),
            str_contains(
                $filename,
                $this->getFileType('PARTNER_QUALIFICATION', 'QUALSCSV')
            ) => app(PartnerQualification::class),
            str_contains($filename, $this->getFileType('SIMPLE', 'SIMPLES')) => app(Simple::class),
            default => null
        };
    }

    /**
     * Clean up temporary files
     * @param string $directory
     * @return void
     */
    private function cleanUp(string $directory): void
    {
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    private function runTestFile(bool $run = false): void
    {
        if ($run) {
            $filesDirectory = base_path(env('ZIP_FILES_DIRECTORY'));
            $testFile = glob($filesDirectory . '/test.txt');
            $this->processCSV($testFile[0], app(Simple::class));
        }
    }
}
