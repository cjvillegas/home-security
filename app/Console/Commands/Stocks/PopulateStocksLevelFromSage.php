<?php

namespace App\Console\Commands\Stocks;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateStocksLevelFromSage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:populate-stockslevel-from-sage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will populate the data of Stocks Level from SAGE.';

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
     * @return int
     */
    public function handle()
    {
        $stockslevel = $this->getStocksLevelData();

        return 0;
    }

    /**
     * Get Stocks Level Data
     *
     * @return string
     */
    public function getStocksLevelData(): string
    {

        $query = "
            SELECT *FROM STOCK_ITEMS LIMIT 10
        ";
        // $query = "
        //     SELECT
        //         StockItem.Code, StockItem.Name, WarehouseItem.ConfirmedQtyInStock -
        //         WarehouseItem.QuantityAllocatedSOP - WarehouseItem.QuantityAllocatedStock AS 'Actual Stock',
        //         WarehouseItem.QuantityOnPOPOrder
        //     FROM
        //         StockItem INNER JOIN
        //         BinItem ON StockItem.ItemID = BinItem.ItemID INNER JOIN
        //         WarehouseItem ON StockItem.ItemID = WarehouseItem.ItemID
        //         AND BinItem.WarehouseItemID = WarehouseItem.WarehouseItemID INNER JOIN
        //         Warehouse ON WarehouseItem.WarehouseID = Warehouse.WarehouseID INNER JOIN
        //         ProductGroup ON StockItem.ProductGroupID = ProductGroup.ProductGroupID INNER JOIN
        //         StockItemNominalCode ON StockItem.ItemID = StockItemNominalCode.ItemID INNER JOIN
        //         NLNominalAccount ON StockItemNominalCode.NominalCodeID = NLNominalAccount.NLNominalAccountID INNER JOIN
        //         NominalUsage ON StockItemNominalCode.NominalUsageID = NominalUsage.NominalUsageID
        //     WHERE
        //         (StockItem.StockItemStatusID = '0') AND (NominalUsage.NominalUsageID = 1)
        //     GROUP BY
        //         ProductGroup.Code, StockItem.Code, ProductGroup.Description,
        //         StockItem.Name, Warehouse.Name, BinItem.BinName, WarehouseItem.ConfirmedQtyInStock,
        //         WarehouseItem.QuantityAllocatedSOP,
        //         WarehouseItem.QuantityAllocatedStock, StockItem.StockUnitName, StockItem.AnalysisCode1,
        //         NLNominalAccount.AccountName, WarehouseItem.QuantityOnPOPOrder, WarehouseItem.UnconfirmedQtyInStock,
        //         BinItem.DateOfLastStockCount, WarehouseItem.DateOfLastSale
        //     HAVING
        //         (Warehouse.Name = 'IPSWICH')
        // ";

        // execute the query
        $stocklevels = DB::connection('stock_sqlsrv')->select($query);
        Log::info($stocklevels);
        // return data as collection
        return collect($stocklevels);
    }
}
