<?php

namespace App\Jobs\Exports;

use App\Exports\StockOrder\StockOrderExport;
use App\Models\StockOrder\StockOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Throwable;

class StockInventoryExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var StockOrder
     */
    private $stockOrder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StockOrder $stockOrder)
    {
        $this->stockOrder = $stockOrder;
    }

    /**
     * Tags to be tracked in the Horizon
     *
     * @return string[]
     */
    public function tags()
    {
        return [
            'stock-inventory-export-job',
            'stock-order: ' . $this->stockOrder->id,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $export = new StockOrderExport($this->stockOrder);
        $export->store("stock-order/{$this->stockOrder->order_no}.csv", 'public', Excel::CSV);
    }

    /**
     * Handle when this job fails
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Stock Order Export', [
            'exception' => $exception,
        ]);
    }
}
