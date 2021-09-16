<?php
namespace App\Services;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    /**
     * Get all the most recent data, then Update existing or Add new Dat
     *
     * @param  mixed $date
     * @return Collection
     */
    public function fetchUpdatedData()
    {
        $date = (new PurchaseOrder())->getMostRecentUpdateDate();
        $whereQuery = $date ? "WHERE POPOrderReturnLine.DateTimeUpdated >= '{$date}'" : '';
        $query = "
            SELECT
                StockItem.Code,
                POPOrderReturn.DocumentDate AS 'POOrderDate',
                POPOrderReturn.RequestedDeliveryDate AS 'POEstimatedDelivery',
                SUM (
                    POPOrderReturnLine.LineQuantity
                ) AS 'POQty',
                POPOrderReturnLine.DateTimeUpdated
            FROM
                StockItemStatus AS StockItemStatus
            INNER JOIN StockItem AS StockItem ON StockItemStatus.StockItemStatusID = StockItem.StockItemStatusID
            INNER JOIN POPOrderReturn AS POPOrderReturn
            INNER JOIN POPOrderReturnLine AS POPOrderReturnLine ON POPOrderReturn.POPOrderReturnID = POPOrderReturnLine.POPOrderReturnID ON StockItem.Code = POPOrderReturnLine.ItemCode
            {$whereQuery}
            GROUP BY
                StockItem.Code,
                StockItem.Name,
                StockItem.StockUnitName,
                POPOrderReturn.DocumentDate,
                POPOrderReturn.RequestedDeliveryDate,
                StockItemStatus.StockItemStatusName,
                StockItem.FreeStockQuantity,
                POPOrderReturn.DocumentStatusID,
                POPOrderReturnLine.ReceiptReturnQuantity,
                POPOrderReturnLine.DateTimeUpdated
            HAVING
                (
                    StockItemStatus.StockItemStatusName LIKE 'Active'
                )
            AND (
                POPOrderReturn.DocumentStatusID LIKE '0%'
            )
            AND (
                POPOrderReturnLine.ReceiptReturnQuantity LIKE '0%'
            )
        ";

        // execute the query
        $purchaseOrders = DB::connection('stock_sqlsrv')->select($query);

        return $purchaseOrders;
    }
}
