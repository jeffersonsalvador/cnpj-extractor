<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 07.12.23 00:15
 */

namespace App\Console\Commands;

use App\Models\Cnae;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Helper\ProgressBar;
use ZipArchive;

class ProcessCNPJ extends Command
{
    protected $signature = 'process:cnpj';
    protected $description = 'Processa arquivos CNPJ em ZIP e insere no banco de dados.';

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
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
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

        try {
            foreach ($csv->getRecords() as $record) {
                $progressBar->advance();
                $record = $this->normalizeData($record);

                $data = array_combine($modelFields, $record);
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $batchData[] = $data;

                if (count($batchData) >= $batchSize) {
                    $model::query()->upsert($batchData, $model->getKeyName());
                    $batchData = []; // Reset the batch
                }
            }

            $progressBar->finish("\n $totalRecords records processed.");

            // Insert the last batch if there are any remaining records
            if (!empty($batchData)) {
                $model::query()->upsert($batchData, $model->getKeyName());
            }
        } catch (\League\Csv\Exception $e) {
            $this->error("Erro na leitura do CSV: " . $e->getMessage());
            Log::error($e);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->error("Erro no banco de dados: " . $e->getMessage());
            Log::error($e);
        }
    }

    private function getFileType(string $type, string $default): string
    {
        return env('FILE_TYPE_' . $type, ".{$default}CSV");
    }

    private function getModelForFile(string $filename): ?Model
    {
        return match (true) {
            str_contains($filename, $this->getFileType('CNAE', 'CNAE')) => app(Cnae::class),
            str_contains($filename, $this->getFileType('COMPANY', 'EMPRE')) => app(Company::class),
            default => null
        };
    }

    private function cleanUp(string $directory): void
    {
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function normalizeData(array $record): array
    {
        return array_map(function($field) {
            // Remove possíveis barras invertidas antes de aspas
            $field = str_replace('\\', '', $field);

            // Converte a codificação de caracteres para UTF-8
            $field = mb_convert_encoding($field, 'UTF-8', 'ISO-8859-1');

            // Substitui vírgula por ponto em valores numéricos
            if (is_numeric(str_replace(',', '.', $field))) {
                $field = str_replace(',', '.', $field);
            }

            return $field;
        }, $record);
    }
}
