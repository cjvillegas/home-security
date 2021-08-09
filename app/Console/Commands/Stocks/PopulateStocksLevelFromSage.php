<?php

namespace App\Console\Commands\Stocks;

use App\Models\StockLevel;
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
        $this->clearTable();
        $stockLevels = $this->getStocksLevelData();
        Log::info(json_encode($stockLevels));
        $chunkCounter = 0;
        // chunk the results to save memory
        foreach ($stockLevels->chunk(100) as $chunk) {
            // foreach to each instance of retrieved order
            $newStockLevels = [];
            foreach ($chunk as $stocklevel) {
                // perform insertion of the order
                $newStockLevels[] = $this->sanitize((array) $stocklevel);
            }

            // do the actual insertion of data
            StockLevel::insert($newStockLevels);

            // increment the execution counter
            $chunkCounter++;

            // execution throttling
            // make the server rest after 10 bulk insert
            if ($chunkCounter % 10 === 0) {
                usleep(100000);
            }
        }

        return 0;
    }

    /**
     * Get Stocks Level Data
     *
     * @return string
     */
    public function getStocksLevelData(): Collection
    {
        $query = "
            SELECT TOP 150
                StockItem.Code, StockItem.Name, WarehouseItem.ConfirmedQtyInStock -
                WarehouseItem.QuantityAllocatedSOP - WarehouseItem.QuantityAllocatedStock AS 'Actual Stock',
                WarehouseItem.QuantityOnPOPOrder
            FROM
                StockItem INNER JOIN
                BinItem ON StockItem.ItemID = BinItem.ItemID INNER JOIN
                WarehouseItem ON StockItem.ItemID = WarehouseItem.ItemID
                AND BinItem.WarehouseItemID = WarehouseItem.WarehouseItemID INNER JOIN
                Warehouse ON WarehouseItem.WarehouseID = Warehouse.WarehouseID INNER JOIN
                ProductGroup ON StockItem.ProductGroupID = ProductGroup.ProductGroupID INNER JOIN
                StockItemNominalCode ON StockItem.ItemID = StockItemNominalCode.ItemID INNER JOIN
                NLNominalAccount ON StockItemNominalCode.NominalCodeID = NLNominalAccount.NLNominalAccountID INNER JOIN
                NominalUsage ON StockItemNominalCode.NominalUsageID = NominalUsage.NominalUsageID
            WHERE
                (StockItem.StockItemStatusID = '0') AND (NominalUsage.NominalUsageID = 1)
            GROUP BY
                ProductGroup.Code, StockItem.Code, ProductGroup.Description,
                StockItem.Name, Warehouse.Name, BinItem.BinName, WarehouseItem.ConfirmedQtyInStock,
                WarehouseItem.QuantityAllocatedSOP,
                WarehouseItem.QuantityAllocatedStock, StockItem.StockUnitName, StockItem.AnalysisCode1,
                NLNominalAccount.AccountName, WarehouseItem.QuantityOnPOPOrder, WarehouseItem.UnconfirmedQtyInStock,
                BinItem.DateOfLastStockCount, WarehouseItem.DateOfLastSale
            HAVING
                (Warehouse.Name = 'IPSWICH')
        ";

        // execute the query
        $stocklevels = DB::connection('stock_sqlsrv')->select($query);
        // return data as collection
        return collect($stocklevels);
    }

    /**
     * This will clear the data from the **orders** table.
     * This method will really do truncation on the orders table, not soft deletion.
     *
     * @return void
     */
    private function clearTable(): void
    {
        StockLevel::truncate();
    }

    /**
     * Sanitize order item coming from SAGE
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $sageStockLevel
     *
     * @return mixed
     */
    private function sanitize(array $sageStockLevel)
    {
        // do a sanity check of the required data
        if (empty($sageStockLevel['Code']) || empty($sageStockLevel['Name']) || empty($sageStockLevel['Actual Stock']) || empty($sageStockLevel['QuantityOnPOPOrder'])) {
            return false;
        }

        $stockLevel['code'] = $sageStockLevel['Code'];
        $stockLevel['name'] = $sageStockLevel['Name'];
        $stockLevel['availablestock'] = $sageStockLevel['Actual Stock'];
        $stockLevel['postock'] = $sageStockLevel['QuantityOnPOPOrder'];
        $stockLevel['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $stockLevel;
    }
}
