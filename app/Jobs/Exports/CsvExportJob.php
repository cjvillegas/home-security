<?php

namespace App\Jobs\Exports;

use App\Exports\ExportDataAsCollectionFromService;
use App\Exports\ExportDataFromQuery;
use App\Models\Export;
use App\Models\User;
use App\Services\ExportPayloadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CsvExportJob implements ShouldQueue
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
     * Create a new job instance.
     *
     * @param User $user
     * @param Export $export
     *
     * @return void
     */
    public function __construct(ExportPayloadService $payload)
    {
        $this->payload = $payload;
        $this->user = $payload->getUser();
        $this->export = $payload->getExport();
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
        // set the file path
        $filePath = "{$this->payload->getPath()}/{$this->export->id}/{$this->payload->getName()}{$this->payload->getType()}";

        // Store on default disk
        $stored = (new ExportDataAsCollectionFromService($this->payload->getService(), $this->payload->getHeaders()))->store($filePath, 'public');

        // check if the file is stored.
        if ($stored) {
            $this->finishExport($filePath);
        }
    }

    /**
     * Finalize the export. This will mark the ending of the export process
     *
     * @param string $path
     *
     * @return void
     */
    private function finishExport(string $path): void
    {
        $url = null;
        Log::info(Storage::disk()->exists($path));
        // check if the file exists and is save after the export
        if (Storage::disk()->exists($path)) {
            $url = Storage::url($path);
        }
        $export = $this->export->refresh();
        $export->status = Export::EXPORT_STATUS_COMPLETED;
        $export->url = $url;
        $export->done_at = now();
        $export->save();
    }

    /**
     * Handle when this job fails
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
        // set the export status to failed
        $this->export->status = Export::EXPORT_STATUS_FAILED;
        $this->export->save();

        Log::error('Error Exporting', [
            'export_id' => $this->export->id,
            'exception' => $exception,
        ]);
    }
}
