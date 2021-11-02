<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Factories\StockOrder\StockOrderFactory;
use App\Factories\StockOrder\StockOrderItemFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockOrder\MoveStockOrderItemRequest;
use App\Http\Requests\StockOrder\MoveStockOrderItemToNewOrderRequest;
use App\Http\Requests\StockOrder\StoreStockOrderItemRequest;
use App\Http\Requests\StockOrder\UpdateStockOrderItemRequest;
use App\Models\StockOrder\StockOrder;
use App\Models\StockOrder\StockOrderItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StockOrderItemController extends Controller
{
    /**
     * @var StockOrderItemFactory
     */
    private $factory;

    /**
     * StockInventoryController constructor
     *
     * @param StockOrderItemFactory
     */
    public function __construct(StockOrderItemFactory $stockOrderFactory)
    {
        $this->factory = $stockOrderFactory;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreStockOrderItemRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreStockOrderItemRequest $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $orderLine = $this->factory->createStockOrderItem(
            $request->getStockOrder(),
            $request->getStockLevel(),
            $user,
            $request->get('order_qty'),
            $request->get('status', StockOrderItem::STATUS_DRAFT)
        );

        return response()->json($orderLine);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateStockOrderItemRequest  $request
     * @param  StockOrderItem $stockOrderItem
     *
     * @return JsonResponse
     */
    public function update(UpdateStockOrderItemRequest $request, StockOrderItem $stockOrderItem): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $orderLine = $this->factory->updateStockOrderItem(
            $stockOrderItem,
            $request->getStockLevel(),
            $user,
            $request->get('order_qty'),
            $request->get('status', StockOrderItem::STATUS_DRAFT)
        );

        return response()->json($orderLine);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(StockOrderItem $stockOrderItem): JsonResponse
    {
        $stockOrderItem->delete();

        return response()->json([
            'message' => 'Order Line deleted.',
            'data' => $stockOrderItem
        ]);
    }

    /**
     * Move order items to a new stock order
     *
     * @param MoveStockOrderItemRequest $request
     *
     * @return JsonResponse
     */
    public function moveItems(MoveStockOrderItemRequest $request): JsonResponse
    {
        $ids = $request->get('order_item_ids');

        StockOrderItem::whereIn('stock_order_items.id', $ids)
            ->update([
                'stock_order_id' => $request->get('order_id')
            ]);

        return response()->json([
            'message' => 'Items moved successfully.',
            'data' => $ids
        ]);
    }

    /**
     * Move order items to a new order. The new order will get created
     * as well in this request
     *
     * @param MoveStockOrderItemToNewOrderRequest $request
     *
     * @return JsonResponse
     */
    public function moveItemsToNewOrder(MoveStockOrderItemToNewOrderRequest $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);
        $ids = $request->get('order_item_ids');

        $factory = new StockOrderFactory();
        $order = $factory->newOrder($user);
        $order->status = StockOrder::STATUS_PENDING;
        $order->save();

        StockOrderItem::whereIn('stock_order_items.id', $ids)
            ->update([
                'stock_order_id' => $order->id
            ]);

        return response()->json([
            'message' => 'Items moved successfully.',
            'data' => $order,
            'stock_order_item_ids' => $ids
        ]);
    }
}
