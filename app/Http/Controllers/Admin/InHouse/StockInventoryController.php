<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Exports\StockOrder\StockOrderExport;
use App\Factories\StockOrder\StockOrderFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockOrder\UpdateStockOrderRequest;
use App\Models\StockLevel;
use App\Models\StockOrder\StockOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockInventoryController extends Controller
{
    /**
     * @var StockOrderFactory
     */
    private $factory;

    /**
     * StockInventoryController constructor
     *
     * @param StockOrderFactory
     */
    public function __construct(StockOrderFactory $stockOrderFactory)
    {
        $this->factory = $stockOrderFactory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.in-house.stock-inventory');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $order = $this->factory->newOrder($user);

        $order = $order->newQuery()
            ->where('id', $order->id)
            ->select()
            ->statusColor()
            ->statusName()
            ->first();

        return response()->json($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  StockOrder $stockOrder
     * @return JsonResponse
     */
    public function show(StockOrder $stockOrder): JsonResponse
    {
        $stockOrder = StockOrder::where('id', $stockOrder->id)
            ->select()
            ->statusColor()
            ->statusName()
            ->with(['orderItems' => function ($query) {
                $query->with(['stockLevel' => function ($query) {
                    $query->select(['stock_levels.id', 'stock_levels.name', 'stock_levels.code', 'stock_levels.available_stock'])
                        ->pendingOrderCount()
                        ->leftJoinStockOrderItem()
                        ->groupBy('stock_levels.id');
                }]);
            }])
            ->first();

        return response()->json([
            'message' => 'Stock order item fetched.',
            'data' => $stockOrder
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  StockOrder $stockOrder
     *
     * @return JsonResponse
     */
    public function update(UpdateStockOrderRequest $request, StockOrder $stockOrder): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $order = $this->factory->updateOrder($stockOrder, $user, $request->all(), true);

        $order = $order->newQuery()
            ->where('id', $order->id)
            ->select()
            ->statusColor()
            ->statusName()
            ->first();

        return response()->json([
            'message' => 'Stock Order updated',
            'data' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  StockOrder $stockOrder
     * @return JsonResponse
     */
    public function destroy(StockOrder $stockOrder): JsonResponse
    {
        $stockOrder->delete();

        return response()->json([
            'message' => 'Stock order deleted.',
            'data' => $stockOrder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  StockOrder $stockOrder
     * @return JsonResponse
     */
    public function cancelStockOrder(StockOrder $stockOrder): JsonResponse
    {
        $stockOrder->cancel();

        $stockOrder = $stockOrder->newQuery()
        ->where('id', $stockOrder->id)
        ->select()
        ->statusColor()
        ->statusName()
        ->first();

        return response()->json([
            'message' => 'Stock order cancelled.',
            'data' => $stockOrder
        ]);
    }

    /**
     * Approve a stock order
     *
     * @param  StockOrder $stockOrder
     *]
     * @return JsonResponse
     */
    public function approveOrder(StockOrder $stockOrder): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $stockOrder->approve($user);

        $stockOrder = $stockOrder->newQuery()
            ->where('id', $stockOrder->id)
            ->select()
            ->statusColor()
            ->statusName()
            ->first();

        $export = new StockOrderExport($stockOrder);
        $export->store("stock-order/{$stockOrder->order_no}.xlsx");

        return response()->json([
            'message' => 'Stock order approved.',
            'data' => $stockOrder
        ]);
    }

    /**
     * Clone a stock order to a new order
     *
     * @param StockOrder $stockOrder
     *
     * @return JsonResponse
     */
    public function cloneOrder(StockOrder $stockOrder): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $order = $this->factory->newOrder($user, StockOrder::STATUS_PENDING);

        $stockOrder->cloneItems($order, $user);

        return response()->json();
    }

    /**
     * Fetch draft orders
     *
     * @return JsonResponse
     */
    public function draftOrders(): JsonResponse
    {
        $draftOrders = StockOrder::draft()
            ->select('stock_orders.*')
            ->leftJoinStockOrderItem()
            ->leftJoinCreator()
            ->creator()
            ->itemsCount()
            ->groupBy('stock_orders.id')
            ->get();

        return response()->json($draftOrders);
    }

    /**
     * Fetch pending orders
     *
     * @return JsonResponse
     */
    public function pendingOrders(): JsonResponse
    {
        $pendingOrders = StockOrder::pending()
            ->select('stock_orders.*')
            ->leftJoinStockOrderItem()
            ->leftJoinCreator()
            ->creator()
            ->itemsCount()
            ->groupBy('stock_orders.id')
            ->get();

        return response()->json($pendingOrders);
    }

    /**
     * Fetch approved orders
     *
     * @return JsonResponse
     */
    public function approvedOrders(): JsonResponse
    {
        $approvedOrders = StockOrder::approved()
            ->select('stock_orders.*')
            ->leftJoinStockOrderItem()
            ->leftJoinCreator()
            ->leftJoinApprover()
            ->creator()
            ->approver()
            ->itemsCount()
            ->groupBy('stock_orders.id')
            ->get();

        return response()->json($approvedOrders);
    }

    /**
     * Count all draft orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function countDraftOrders(): JsonResponse
    {
        $totalPendingOrders = StockOrder::draft()
            ->distinct()
            ->count('stock_orders.id');

        return response()->json($totalPendingOrders);
    }


    /**
     * Count all pending orders
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function countPendingOrders(Request $request): JsonResponse
    {
        $totalPendingOrders = StockOrder::pending()
            ->distinct()
            ->count('stock_orders.id');

        return response()->json($totalPendingOrders);
    }

    /**
     * Count all pending orders
     *
     * @return JsonResponse
     */
    public function countApprovedOrders(): JsonResponse
    {
        $totalApprovedOrders = StockOrder::approved()
            ->distinct()
            ->count('stock_orders.id');

        return response()->json($totalApprovedOrders);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchStockLevels(Request $request): JsonResponse
    {
        $searchString = $request->get('searchString');
        $toNeglectIds = $request->get('ids');

        $stockLevels = StockLevel::query()
            ->select('stock_levels.*')
            ->pendingOrderCount()
            ->leftJoinStockOrderItem()
            ->when(!empty($searchString), function ($query) use ($searchString) {
                $query->where('stock_levels.code', 'like', "%{$searchString}%");
            })
            ->when($toNeglectIds, function ($query) use ($toNeglectIds) {
                $query->whereNotIn('stock_levels.id', $toNeglectIds);
            })
            ->limit(25)
            ->groupBy('stock_levels.id')
            ->orderBy('code')
            ->get();

        return response()->json($stockLevels);
    }
}
