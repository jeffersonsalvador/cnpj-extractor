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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ProcessCsvRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param string $redisKey
     * @param string $model
     */
    public function __construct(
        protected readonly string $redisKey,
        private readonly string $model,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $model = new $this->model;
        while ($batchData = Redis::rpop($this->redisKey)) {
            $records = json_decode($batchData, true);
            $model::query()->upsert($records, $model->getKeyName());
        }
    }

    /**
     * Triggered when the job fails.
     *
     * @return int[]
     */
    public function backoff()
    {
        return [10, 30, 60];
    }
}
