<?php

namespace App\Jobs\Exports;

use App\Services\ExportPayloadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ShiftPerformanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ExportPayloadService
     */
    private $payload;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var $dateRange
     */
    private $dateRange;


    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Export $export
     *
     * @return void
     */
    public function __construct(ExportPayloadService $payload, $dateRange)
    {
        $this->payload = $payload;
        $this->user = $payload->getUser();
        $this->export = $payload->getExport();
        $this->dateRange = $dateRange;
    }

    /**
     * Tags to be tracked in the Horizon
     *
     * @return string[]
     */
    public function tags()
    {
        return [
            'csv-export-job',
            'user: ' . $this->user->id,
            'export: ' . $this->export->id,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = $this->payload->getService();
        $data = $service->getData('export');
        $performances = $data;

        $this->exportFromCollection($performances);
    }

    public function exportFromCollection($performances)
    {

    }

}
