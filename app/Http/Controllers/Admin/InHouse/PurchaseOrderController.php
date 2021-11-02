<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $purchaseOrders = PurchaseOrder::select(
            [
                'purchase_orders.id',
                'purchase_orders.code',
                'sl.name AS description',
                'sl.available_stock AS in_stock_now',
                'sl.product_category AS product_category',
                'purchase_orders.quantity AS arriving_stock',
                'purchase_orders.estimated_delivery AS delivery_date'
            ])
            ->join('stock_levels AS sl', 'sl.code', 'purchase_orders.code')
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('purchase_orders.estimated_delivery', [$start, $end]);
            })
            ->get();

        return response()->json($purchaseOrders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
