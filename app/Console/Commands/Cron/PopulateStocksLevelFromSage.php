<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\StockLevel;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopulateStocksLevelFromSage extends CronDatabasePopulator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:populate-stock-levels-from-sage';

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

        $this->table = 'stock_levels';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $this->truncateTable();

            $stockLevels = $this->getDataFromBlind();

            $chunkCounter = 0;
            // chunk the results to save memory
            foreach ($stockLevels->chunk(100) as $chunk) {
                // foreach to each instance of retrieved order
                $newStockLevels = [];
                foreach ($chunk as $stocklevel) {
                    // perform insertion of the order
                    $sanitized = $this->sanitize((array) $stocklevel);

                    // sanity check
                    if ($sanitized !== null) {
                        $newStockLevels[] = $sanitized;
                    }
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
        } catch (Exception $error) {
            $this->sendFailedNotification('Populate Stock Level From Sage', $error);
        }
    }

    /**
     * Truncate the given table. This method disables the foreign key check
     * and enables it again after the truncation
     */
    private function truncateTable()
    {
        Schema::disableForeignKeyConstraints();

        DB::connection('mysql')
            ->table($this->table)
            ->truncate();

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Get Stocks Level Data
     *
     * @return string
     *
     */
    public function getDataFromBlind(): Collection
    {
        $query = "
            SELECT
                StockItem.Code, StockItem.Name, WarehouseItem.ConfirmedQtyInStock -
                WarehouseItem.QuantityAllocatedSOP - WarehouseItem.QuantityAllocatedStock AS 'Actual Stock',
                WarehouseItem.QuantityOnPOPOrder, NLNominalAccount.AccountName AS [ProductCategory]
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
     * Sanitize order item coming from SAGE
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $sageStockLevel
     *
     * @return array|null
     */
    protected function sanitize(array $sageStockLevel): ?array
    {
        // do a sanity check of the required data
        if (empty($sageStockLevel['Code']) || empty($sageStockLevel['Name']) || empty($sageStockLevel['Actual Stock']) || empty($sageStockLevel['QuantityOnPOPOrder'])) {
            return null;
        }

        $stockLevel['code'] = $sageStockLevel['Code'];
        $stockLevel['name'] = $sageStockLevel['Name'];
        $stockLevel['available_stock'] = $sageStockLevel['Actual Stock'];
        $stockLevel['po_stock'] = $sageStockLevel['QuantityOnPOPOrder'];
        $stockLevel['product_category'] = $sageStockLevel['ProductCategory'];
        $stockLevel['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $stockLevel;
    }
}
