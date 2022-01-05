<?php

namespace App\Console\Commands\Cron;

use App\Jobs\GeneratePickingListJob;
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
            $warehouseItems = $this->getWarehouseData($orderNos);

            /**
             * loop through all the retrieved items from sage, why?
             * it's better to only get what's present for optimization
             */
            foreach ($sageOrders as $order) {
                $stockOrder = $chunk->firstWhere('order_no', $order->CustomerDocumentNo);

                if (!empty($order)) {
                    $stockOrder->sage_order_no = $order->DocumentNo;
                    $saved = $stockOrder->save();

                    $warehouseItem = $warehouseItems->firstWhere('CustomerDocumentNo', $order->CustomerDocumentNo);

                    /**
                     * If the sage_order_no has been updated successfully
                     * then we generate/send an email for picking list
                     */
                    if ($saved && !empty($warehouseItem)) {
                        $warehouseItem = (array) $warehouseItem;
                        GeneratePickingListJob::dispatch($stockOrder, $warehouseItem)->onQueue('default');
                    }
                }
            }
        }
    }

    /**
     * @param array $orderNos
     *
     * @return SupCollection
     */
    private function getSageOrders(array $orderNos): SupCollection
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
     * Get warehouse items from SAGE
     *
     * @param array $orderNos
     *
     * @return SupCollection
     */
    private function getWarehouseData(array $orderNos)
    {
        $imploded = implode(', ', $orderNos);
        $whereInOrderNos = "({$imploded})";

        $query = "
            SELECT
               SOPOrderReturn.DocumentNo,
               SOPOrderReturn.CustomerDocumentNo,
               SOPOrderReturn.DocumentDate,
               BinItem.BinName as BinLocation,
               SOPOrderReturnLine.ItemCode,
               SOPOrderReturnLine.LineQuantity,
               StockItem.Name AS Description,
               WarehouseItem.ConfirmedQtyInStock + WarehouseItem.UnconfirmedQtyInStock AS [QtyInStock]
            FROM  WarehouseItem
                INNER JOIN Warehouse ON WarehouseItem.WarehouseID = Warehouse.WarehouseID
                INNER JOIN BinItem ON WarehouseItem.WarehouseItemID = BinItem.WarehouseItemID
                INNER JOIN SOPOrderReturn
                INNER JOIN SOPOrderReturnLine ON SOPOrderReturn.SOPOrderReturnID = SOPOrderReturnLine.SOPOrderReturnID
                INNER JOIN StockItem ON SOPOrderReturnLine.ItemCode = StockItem.Code ON WarehouseItem.ItemID = StockItem.ItemID
            WHERE
                (SOPOrderReturn.CustomerDocumentNo IN {$whereInOrderNos})
                AND (Warehouse.Name = 'IPSWICH')
        ";

        // execute the query
        $warehouseItems = DB::connection('sage_order')->select($query);

        // return data as collection
        return collect($warehouseItems);
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
