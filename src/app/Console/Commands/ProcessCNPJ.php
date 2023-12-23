<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 07.12.23 00:15
 */

namespace App\Console\Commands;

use App\Jobs\ProcessCsvRecords;
use App\Models\City;
use App\Models\Cnae;
use App\Models\Company;
use App\Models\Country;
use App\Models\LegalNature;
use App\Models\Partner;
use App\Models\PartnerQualification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
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
     * Execute the console command.
     */
    /**
     */
    public function handle(): void
    {
        $zipFilesDirectory = base_path(env('ZIP_FILES_DIRECTORY'));
        $zipFiles = glob($zipFilesDirectory . '/*.zip');

        $progressBar = new ProgressBar($this->output, count($zipFiles));

        try {
            foreach ($zipFiles as $zipFile) {
                $this->processZipFile($zipFile);
                $progressBar->advance();
            }
        } catch (Exception $e) {
            $this->error('Erro no processamento: ' . $e->getMessage());
            Log::error($e);
        }

        $progressBar->finish();
        $this->info("\nProcess finished!");
    }

    /**
     * Process a ZIP file
     * @param string $zipFilePath
     * @return void
     */
    private function processZipFile(string $zipFilePath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath) === true) {
            $tempDir = sys_get_temp_dir();
            $zip->extractTo($tempDir);
            $filename = $zip->getNameIndex(0);
            $model = $this->getModelForFile($filename);
            if ($model === null) {
                $this->error("\nTipo de arquivo não suportado: $filename");
                return;
            }

            try {
                $this->processCSV($tempDir . '/' . $filename, $model);
            } catch (InvalidArgument $e) {
                $this->error("Erro de argumento inválido: " . $e->getMessage());
                Log::error("Erro de argumento inválido no processamento do CSV: " . $e->getMessage());
            } catch (UnavailableStream $e) {
                $this->error("Erro de stream indisponível: " . $e->getMessage());
                Log::error("Erro de stream indisponível no processamento do CSV: " . $e->getMessage());
            } catch (Exception $e) {
                $this->error("Erro de exceção geral: " . $e->getMessage());
                Log::error("Erro de exceção geral no processamento do CSV: " . $e->getMessage());

            }

            $zip->close();
            $this->cleanUp($tempDir);
        } else {
            $this->error("Não foi possível abrir o arquivo: $zipFilePath");
        }
    }

    /**
     * Process a CSV file
     * @param string $filePath
     * @param Model $model
     * @return void
     * @throws Exception
     * @throws InvalidArgument
     * @throws UnavailableStream
     */
    private function processCSV(string $filePath, Model $model): void
    {
        $this->info("\nProcessing CSV file: " . basename($filePath));

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setEnclosure('"');
        $csv->setEscape('');
        $totalRecords = $csv->count();

        $progressBar = new ProgressBar($this->output, $totalRecords);
        $batchData = [];
        $batchSize = env('BATCH_SIZE', 1000);
        $modelFields = $model->getFillable();
        dd($modelFields);
        $redisKey = 'processed_' . $model->getTable();

        foreach ($csv->getRecords() as $record) {
            $progressBar->advance();
            $record = $this->normalizeData($record);
            $data = array_combine($modelFields, $record);
            $batchData[] = $data;

            if (count($batchData) >= $batchSize) {
                $this->storeAndDispatchJob($redisKey, $batchData, $model);
                $batchData = []; // Reset the batch
            }
        }

        $progressBar->finish();

        // Insert the last batch if there are any remaining records
        if (!empty($batchData)) {
            $this->storeAndDispatchJob($redisKey, $batchData, $model);
        }
    }

    /**
     * Store the batch data and dispatch a job
     *
     * @param string $redisKey
     * @param array $batchData
     * @param Model $model
     * @return void
     */
    private function storeAndDispatchJob(string $redisKey, array $batchData, Model $model): void
    {
        $uid = uniqid();
        Redis::lpush("$redisKey-$uid", json_encode($batchData));
        ProcessCsvRecords::dispatch("$redisKey-$uid", get_class($model));
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
            str_contains($filename, $this->getFileType('ESTABLISHMENT', 'ESTABELE')) => app(Company::class),
            str_contains($filename, $this->getFileType('LEGAL_NATURE', 'NATJUCSV')) => app(LegalNature::class),
            str_contains($filename, $this->getFileType('PARTNER', 'SOCIOCSV')) => app(Partner::class),
            str_contains(
                $filename,
                $this->getFileType('PARTNER_QUALIFICATION', 'QUALSCSV')
            ) => app(PartnerQualification::class),
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
     * Normalize data
     * @param array $record
     * @return array
     */
    private function normalizeData(array $record): array
    {
        return array_map(function($field) {
            // Remove backslashes
            $field = str_replace('\\', '', $field);

            // Convert ISO-8859-1 to UTF-8
            $field = mb_convert_encoding($field, 'UTF-8', 'ISO-8859-1');

            // Replace commas with dots in numeric fields
            if (is_numeric(str_replace(',', '.', $field))) {
                $field = str_replace(',', '.', $field);
            }

            return $field;
        }, $record);
    }
}
