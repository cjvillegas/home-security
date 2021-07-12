<?php

namespace App\Repositories\Orders;

use App\Models\Order;

class OrderRepository
{
    /**
     * Searches orders based on the passed field name
     *
     * @param string $field
     * @param string $searchString
     *
     * @return array
     */
    public function searchOrdersByField(string $field, string $searchString): array
    {
        // sanity checks if the required fields are provided
        if (!$field || !$searchString) {
            return [];
        }

        // do the actual query
        $orders = Order::where($field, 'like', "%$searchString%")
            ->select('id', 'blind_id', 'order_no', 'customer', 'serial_id', 'customer_order_no')
            ->limit(25)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return $orders;
    }
}
