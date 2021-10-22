<?php

namespace App\Factories\StockOrder;

use App\Interfaces\BaseFactoryInterface;
use App\Models\StockOrder\StockOrder;
use App\Models\User;

/**
 * Factory class for StockOrder model.
 *
 * @author Chaps
 */
class StockOrderFactory implements BaseFactoryInterface
{
    /**
     * Generate new draft order
     *
     * @param User $user
     *
     * @return StockOrder
     */
    public function newOrder(User $user): StockOrder
    {
        $stockOrder = new StockOrder;
        $stockOrder->status = StockOrder::STATUS_DRAFT;
        $stockOrder->created_by = $user->id;
        $stockOrder->save();

        return $stockOrder;
    }
}
