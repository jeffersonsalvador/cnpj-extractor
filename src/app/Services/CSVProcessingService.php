<?php
/**
 * @author    Jefferson Costa
 * @copyright 2024, Málaga
 * @package   cnpj-extractor
 *
 * Created using PhpStorm at 20/01/2024 06:51
 */

namespace App\Services;

use App\Jobs\ProcessCsvRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class CSVProcessingService
{
    /**
     * Process a CSV file
     * @param  string  $filePath
     * @param  Model  $model
     * @param  OutputInterface  $output
     * @return void
     * @throws Exception
     * @throws InvalidArgument
     * @throws UnavailableStream
     */
    public function process(string $filePath, Model $model, OutputInterface $output): void
    {
        $csv = Reader::createFromPath($filePath);
        $csv->setDelimiter(';');
        $csv->setEnclosure('"');
        $csv->setEscape('');
        $totalRecords = $csv->count();

        $progressBar = new ProgressBar($output, $totalRecords);
        $progressBar->start();

        $batchData = [];
        $batchSize = env('BATCH_SIZE', 1000);
        $redisKey = 'processed_' . $model->getTable();

        foreach ($csv->getRecords() as $record) {
            $record = $this->normalizeData($record, $model);
            $data = array_combine($model->getFillable(), $record);

            $batchData[] = $data;

            if (count($batchData) >= $batchSize) {
                $this->storeAndDispatchJob($redisKey, $batchData, $model);
                $progressBar->advance(count($batchData));
                $batchData = []; // Reset the batch
            }
        }

        // Insert the last batch if there are any remaining records
        if (!empty($batchData)) {
            $this->storeAndDispatchJob($redisKey, $batchData, $model);
            $progressBar->advance(count($batchData));
        }

        $progressBar->finish();
        $output->writeln("\nCSV processing finished");
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

    private function normalizeData(array $record, Model $model): array
    {
        $normalizedRecord = [];
        $fields = $model->getFillable(); // Obtém os nomes dos campos
        $dateFields = method_exists($model,'getDateFields') ? $model->getDateFields() : []; // Obtém os campos de data
        $recordWithKeys = array_combine($fields, $record);

        foreach ($recordWithKeys as $key => $value) {
            if (in_array($key, $dateFields)) {
                $normalizedRecord[$key] = $this->normalizeDateField($value);
            } else {
                $normalizedRecord[$key] = $this->normalizeField($value);
            }
        }

        return $normalizedRecord;
    }

    private function normalizeDateField(?string $value): ?string
    {
        if ($value === '00000000' || $value === '') {
            return null;
        }

        return date('Y-m-d', strtotime($value));
    }

    /**
     * @param string|null $value
     * @return array|false|string|string[]|null
     */
    private function normalizeField(?string $value)
    {
        if ($value === '') {
            return null;
        }

        $exclude = [':', '_', '-', ' ', ',', '+', '.', '?', '!', '´'];
        $field = str_replace(array("'", '"', "\\"), '', trim($value));
        $field = Str::startsWith($field, collect($exclude)) ? substr($field, 1) : $field;
        $field = Str::startsWith($field, collect($exclude)) ? substr($field, 1) : $field;
        $field = mb_convert_encoding($field, 'UTF-8', 'ISO-8859-1');

        if (is_numeric(str_replace(',', '.', $field))) {
            $field = str_replace(',', '.', $field);
        }

        return trim($field);
    }
}
