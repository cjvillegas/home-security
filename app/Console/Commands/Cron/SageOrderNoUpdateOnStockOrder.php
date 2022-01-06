<?php

namespace App\Console\Commands\Cron;

use App\Models\StockOrder\StockOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupCollection;
use Illuminate\Support\Facades\DB;

class SageOrderNoUpdateOnStockOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:update-sage-order-no';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Sage order no in approved stock orders.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $stockOrders = $this->getOrders();

        if ($stockOrders->isEmpty()) {
            return;
        }

        // chunk orders
        foreach ($stockOrders->chunk(100) as $chunk) {
            $orderNos = $chunk->pluck('order_no')->toArray();
            $sageOrders = $this->getSageOrders($orderNos);

            /**
             * loop through all the retrieved items from sage, why?
             * it's better to only get what's present for optimization
             */
            foreach ($sageOrders as $order) {
                $stockOrder = $chunk->firstWhere('order_no', $order->CustomerDocumentNo);

                if (!empty($order)) {
                    $stockOrder->sage_order_no = $order->DocumentNo;
                    $stockOrder->save();
                }
            }
        }

        return;
    }

    /**
     * @param array $orderNos
     *
     * @return SupCollection
     */
    public function getSageOrders(array $orderNos): SupCollection
    {
        $imploded = implode(', ', $orderNos);
        $whereInOrderNos = "({$imploded})";

        $query = "
            SELECT
                DocumentNo,
                CustomerDocumentNo
                FROM  SOPOrderReturn
            WHERE SOPOrderReturn.CustomerDocumentNo IN {$whereInOrderNos}
        ";

        // execute the query
        $stockOrders = DB::connection('sage_order')->select($query);

        // return data as collection
        return collect($stockOrders);
    }

    /**
     * Get all approved orders where their sage order no is not yet populated
     *
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return StockOrder::where('status', StockOrder::STATUS_APPROVED)
            ->whereNull('sage_order_no')
            ->get();
    }
}
