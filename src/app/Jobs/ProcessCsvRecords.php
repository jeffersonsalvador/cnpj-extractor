<?php
/**
 * @author    Jefferson Costa
 * @copyright 2023, Hamburg
 * @package   cnpj-dados-publicos-receita-federal
 *
 * Created using PhpStorm at 08.12.23 09:50
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProcessCsvRecords extends Job
{
    use InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * @param string $redisKey
     * @param Model $model
     */
    public function __construct(
        protected readonly string $redisKey,
        private readonly Model $model,
    ) {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        Log::info("Iniciando o processamento do Job");
        while ($batchData = Redis::rpop($this->redisKey)) {
            $records = json_decode($batchData, true);
            $this->model::query()->upsert($records, $this->model->getKeyName());
        }
    }
}