<?php

namespace App\Factories\StockOrder;

use App\Factories\SbgBaseFactory;
use App\Models\StockOrder\StockOrder;
use App\Models\User;

/**
 * Factory class for StockOrder model.
 *
 * @author Chaps
 */
class StockOrderFactory extends SbgBaseFactory
{
    /**
     * Generate a new order
     *
     * @param User $user
     *
     * @return StockOrder
     */
    public function newOrder(User $user, int $status = StockOrder::STATUS_DRAFT): StockOrder
    {
        $stockOrder = new StockOrder;
        $stockOrder->status = $status;
        $stockOrder->created_by = $user->id;
        $stockOrder->save();

        return $stockOrder;
    }

    /**
     * @param StockOrder $stockOrder
     * @param User $user
     * @param array $data
     * @param bool $updateOrderItems
     *
     * @return StockOrder
     */
    public function updateOrder(StockOrder $stockOrder, User $user, array $data, bool $updateOrderItems = false): StockOrder
    {
        $stockOrder->status = $data['status'] ?? $stockOrder->status;
        $stockOrder->updated_by = $user->id;
        $stockOrder->save();

        // this will sync the order item status to the parent order
        if ($updateOrderItems) {
            $stockOrder->syncOrderItemStatus();
        }

        return $stockOrder;
    }
}
