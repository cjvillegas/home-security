<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ProcessSequence\ProcessSequence;
use App\Models\Scanner;
use App\Models\ShiftAssignment;
use App\Repositories\Orders\OrderRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    use CsvImportTrait;

    /**
     * OrdersController constructor.
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.index', [
            'type' => $request->get('type')
        ]);
    }

    public function vieworderno($order_no)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = Order::where('order_no', $order_no)->first();
        $orders = Order::where('order_no', $order_no)->paginate(50);
        $ordersem = DB::table('order')
                     ->select('employees.fullname', 'scanners.scannedtime', 'processes.name', 'scanners.blindid', 'processes.name', 'orders.order_no', 'orders.customer', 'orders.cust_ord_ref')
                     ->from('employees')
                     ->join('scanners', 'employees.barcode', '=', 'scanners.employeeid')
                     ->join('processes', 'processes.barcode', '=', 'scanners.processid')
                     ->join('orders', 'orders.blindid', '=', 'scanners.blindid')
                     ->where('order_no', $order_no)
                     ->get();

        return view('admin.orders.vieworderno', compact('order', 'orders', 'ordersem'));
    }

    /**
     * Fetch list of orders with pagination and searching
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function fetch(Request $request)
    {
        $size = $request->get('size');
        $searchString = $request->get('searchString');

        $orders = Order::groupBy('order_no');

        if ($searchString) {
            $orders->where(function($q) use ($searchString) {
                $q->where('order_no', 'like', "%$searchString%");
            });
        }

        $orders = $orders->orderBy('id', 'asc')->paginate($size);


        return response()->json($orders);
    }

    /**
     * Searches orders based on the passed field name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchOrdersByField(Request $request)
    {
        $field = $request->get('field');
        $searchString = $request->get('searchString');

        if (!$field || !$searchString) {
            return response()->json([]);
        }

        return response()->json($this->repository->searchOrdersByField($field, $searchString));
    }

    /**
     * Retrieve orders with same order_no
     *
     * @param string $order_no
     *
     * @return JsonResponse
     */
    public function getOrderDetails(Request $request, $to_search): JsonResponse
    {
        $orders= Order::where($request->get('field'), $to_search)
            ->select([
                'orders.*',
                DB::raw('COUNT(DISTINCT serial_id) AS total_blinds')
            ])
            ->with(['scanners' => function ($query) {
                $query->with(['employee' => function ($query) {
                    $query->with(['shift', 'team']);
                }, 'process', 'qcFault'])
                ->orderBy('scannedtime', 'asc');
            }])
            ->groupBy('order_no')
            ->first();

        return response()->json($orders);
    }

    /**
     * Retrieve order trackings
     *
     * @return JsonResponse
     */
    public function fetchTrackings(Request $request): JsonResponse
    {
        $orderTrackings = OrderTracking::where('order_no', $request->order_no)
            ->get();
        return response()->json(['orderTrackings' => $orderTrackings]);
    }

    /**
     * Retrieve shift assignments data of an order based on the given order no
     *
     * @param Request $request
     * @param $orderNo
     *
     * @return JsonResponse
     */
    public function getOrderPlannedWork(Request $request, $orderNo): JsonResponse
    {
        $shiftAssignments = ShiftAssignment::select([
                'shift_assignments.id',
                'shift_assignments.serial_id',
                'folder_name',
                'scheduled_date',
                'work_date'
            ])
            ->join('orders AS o', 'o.serial_id', 'shift_assignments.serial_id')
            ->where('o.order_no', $orderNo)
            ->get();

        return response()->json($shiftAssignments);
    }

    /**
     * Get scanners data of an order based on the provided order no
     *
     * @param Request $request
     * @param $orderNo
     *
     * @return JsonResponse
     */
    public function getOrderScanners(Request $request, $orderNo): JsonResponse
    {
        $scanners = Scanner::select([
                'scanners.*',
                'o.serial_id AS serial_id',
                'e.fullname AS employee_name',
                'e.id AS employee_id',
                'p.name AS process_name',
                'p.id AS process_id'
            ])
            ->with('qcFault')
            ->join('orders AS o', 'o.serial_id', 'scanners.blindid')
            ->leftJoin('employees AS e', 'e.barcode', 'scanners.employeeid')
            ->leftJoin('processes AS p', 'p.barcode', 'scanners.processid')
            ->where('o.order_no', $orderNo)
            ->groupBy('scanners.id')
            ->get();

        return response()->json($scanners);
    }

    /**
     * Get the process sequences that will be used by the particular orders.
     * The condition will based on the process name and on the ID. Pfft!
     *
     * @param Request $request
     * @param $orderNo
     *
     * @return JsonResponse
     */
    public function getOrderProcessSequences(Request $request, $orderNo): JsonResponse
    {
        $processSequences = ProcessSequence::select([
                'process_sequences.*',
                'o.id AS order_id',
                'o.order_no AS order_no',
            ])
            ->with(['steps' => function ($query) {
                $query->with('process');
            }])
            ->join('orders AS o', 'o.product_type', 'process_sequences.name')
            ->where('o.order_no', $orderNo)
            ->groupBy('process_sequences.id')
            ->get();

        return response()->json($processSequences);
    }

    /**
     * Retrieve list of orders based on the provided order no
     *
     * @param $orderNo
     *
     * @return JsonResponse
     */
    public function getOrdersByOrderNo($orderNo): JsonResponse
    {
        $orders = Order::where('order_no', $orderNo)
            ->with(['scanners' => function ($query) {
                $query->with('employee', 'process');
            }])
            ->get();

        return response()->json($orders);
    }

    /**
     * Update Product Type
     *
     * @param  mixed $request
     * @param  mixed $order
     *
     * @return JsonResponse
     */
    public function updateProductType(Request $request, Order $order): JsonResponse
    {
        $order->product_type = $request->product_type;
        $order->save();

        return response()->json(['message' => 'Successfully saved changes']);
    }
}
