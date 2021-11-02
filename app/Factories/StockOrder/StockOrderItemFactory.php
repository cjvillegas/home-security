<?php

namespace App\Factories\StockOrder;

use App\Factories\SbgBaseFactory;
use App\Models\StockLevel;
use App\Models\StockOrder\StockOrder;
use App\Models\StockOrder\StockOrderItem;
use App\Models\User;

/**
 * Factory class for StockOrderItem model.
 *
 * @author Chaps
 */
class StockOrderItemFactory extends SbgBaseFactory
{
    /**
     * Create new stock order item
     *
     * @param StockOrder $stockOrder
     * @param StockLevel $stockLevel
     * @param User $user
     * @param int $orderQty
     * @param int $status
     *
     * @return StockOrderItem
     */
    public function createStockOrderItem(StockOrder $stockOrder, StockLevel $stockLevel, User $user, int $orderQty, int $status): StockOrderItem
    {
        $stockOrderItem = new StockOrderItem();
        $stockOrderItem->stock_order_id = $stockOrder->id;
        $stockOrderItem->stock_level_id = $stockLevel->id;
        $stockOrderItem->order_qty = $orderQty;
        $stockOrderItem->status = $status;
        $stockOrderItem->created_by = $user->id;
        $stockOrderItem->save();

        return $stockOrderItem;
    }

    /**
     * Update a stock order item
     *
     * @param StockOrderItem $stockOrderItem
     * @param StockLevel $stockLevel
     * @param User $user
     * @param int $orderQty
     * @param int $status
     *
     * @return StockOrderItem
     */
    public function updateStockOrderItem(
        StockOrderItem $stockOrderItem,
        StockLevel $stockLevel,
        User $user,
        int $orderQty,
        int $status): StockOrderItem {

        $stockOrderItem->stock_level_id = $stockLevel->id;
        $stockOrderItem->order_qty = $orderQty;
        $stockOrderItem->status = $status;
        $stockOrderItem->updated_by = $user->id;
        $stockOrderItem->save();

        return $stockOrderItem;

    }
}
