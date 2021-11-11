<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QcRemakeCheckerController extends Controller
{
    /**
     * QC Remake Index
     *
     * @return void
     */
    public function index()
    {
        return view('admin.quality-control.remake');
    }

    public function getOrders(Request $request)
    {
        Log::info($request->all());
        $orders = Order::where('order_no', $request->order_no)
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
