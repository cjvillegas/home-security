<?php

namespace App\Factories\Order;

use App\Factories\SbgBaseFactory;
use App\Models\Order;

class OrderFactory extends SbgBaseFactory
{
    /**
     * Create a new order in our Production. The main purpose of this method
     * is to cater data coming from the Blind Data
     *
     * @param array $blindOrder
     *
     * @return Order
     */
    public function createOrderFromBlind(array $blindOrder): Order
    {
        $newOrder = new Order;
        $newOrder->serial_id = $blindOrder['SerialID'];
        $newOrder->order_no = $blindOrder['OrderNo'];
        $newOrder->customer = $blindOrder['Customer'];
        $newOrder->customer_ref = $blindOrder['CustRef'];
        $newOrder->customer_order_no = $blindOrder['CustNo'];
        $newOrder->product_type = $blindOrder['ProductType'];
        $newOrder->blind_type = $blindOrder['ProductCode'];
        $newOrder->ordered_at = $blindOrder['Ordered'];
        $newOrder->required_date = $blindOrder['RequiredDate'];
        $newOrder->order_entered_by = $blindOrder['OrderEnteredBy'];
        $newOrder->account_code = $blindOrder['AccountCode'];
        $newOrder->width = $blindOrder['Width'];
        $newOrder->drop = $blindOrder['Drop'];
        $newOrder->stock_code = $blindOrder['StockCode'];
        $newOrder->fabric_range = $blindOrder['FabricRange'];
        $newOrder->color = $blindOrder['Colour'];
        $newOrder->item_price = $blindOrder['ItemPrice'];
        $newOrder->created_at = now('UTC')->format('Y-m-d H:i:s');
        $newOrder->save();

        return $newOrder;
    }
}
