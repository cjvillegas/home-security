<?php

namespace App\Http\Controllers\Admin;

use App\Factories\Order\OrderFactory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\Order\ImportOrderFromBlindRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ProcessSequence\ProcessSequence;
use App\Models\Scanner;
use App\Models\ShiftAssignment;
use App\Models\User;
use App\Repositories\Orders\OrderRepository;
use App\Services\CsvExporterService;
use App\Services\Reports\OrderDataService;
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
     * @var OrderFactory
     */
    private $factory;

    /**
     * OrdersController constructor.
     */
    public function __construct(OrderRepository $repository, OrderFactory $orderFactory)
    {
        $this->repository = $repository;
        $this->factory = $orderFactory;
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

    /**
     * Page where user could do order searching
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function orderSearch(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.order-search', [
            'type' => $request->get('type')
        ]);
    }

    /**
     * Get orders data and list it in our front-end
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function orderList(Request $request): JsonResponse
    {
        $service = new OrderDataService($request->all());
        $orders = $service->getData('list');

        return response()->json($orders);
    }

    /**
     * Import order directly from blind data
     *
     * @param ImportOrderFromBlindRequest $request
     *
     * @return JsonResponse
     */
    public function importFromBlind(ImportOrderFromBlindRequest $request): JsonResponse
    {
        $serialId = $request->get('serial_id');

        // initialize the query
        $query = $this->repository->generateBaseQueryForBlindData(
            "sdl.id = {$serialId}",
            1
        );

        // execute the query
        $order = DB::connection('sqlsrv')->select($query);

        // make sure that the order exists in the BlindData
        if (empty($order)) {
            return response()->json([
                'errors' => [
                    'serial_id' => "Order doesn't exist in Blind Data."
                ]
            ], 422);
        }

        // get the very first instance
        $sageOrder = $order[0];

        // do a sanity check of the required data
        if (empty($sageOrder->SerialID) ||
            empty($sageOrder->OrderNo) ||
            empty($sageOrder->Customer) ||
            empty($sageOrder->ProductType) ||
            empty($sageOrder->ProductCode)) {

            return response()->json([
                'message' => [
                    'serial_id' => "Invalid order data coming from Blind Data."
                ]
            ], 422);
        }

        $newOrder = $this->factory->createOrderFromBlind((array) $sageOrder);

        return response()->json($newOrder);
    }

    /**
     * Exports orders data to a xlsx file
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function exportOrders(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $headers = (array) json_decode($request->get('headers'));
        $filters = (array) json_decode($request->get('filters'));
        $service = new OrderDataService($filters);
        $exporter = new CsvExporterService($user);
        $exporter->setName('Orders')
            ->setPath('exports')
            ->setHeaders($headers)
            ->export($service);

        return response()->json([
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.',
            'success' => true
        ]);
    }

    public function vieworderno($order_no)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order = Order::where('order_no', $order_no)->first();
        $orders = Order::where('order_no', $order_no)->paginate(50);
        $ordersem = DB::table('order')
                     ->select('employees.fullname', 'employees.barcode', 'scanners.scannedtime', 'processes.name', 'scanners.blindid', 'processes.name', 'orders.order_no', 'orders.customer', 'orders.cust_ord_ref')
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
        $field = $request->get('field');

        $orders= Order::where("orders.{$field}", $to_search)
            ->select([
                'orders.*',
                'oi.invoice_no',
                'oi.date AS invoice_date',
                DB::raw('COUNT(DISTINCT orders.serial_id) AS total_blinds')
            ])
            ->leftJoin('order_invoices AS oi', 'oi.order_no', 'orders.order_no')
            ->with(['scanners' => function ($query) {
                $query->with(['employee' => function ($query) {
                    $query->with(['shift', 'team']);
                }, 'process', 'qcFault'])
                ->orderBy('scannedtime', 'asc');
            }])
            ->groupBy('orders.order_no')
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
                'e.barcode',
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

    public function getAllBlindType(): JsonResponse
    {
        $products = Order::query()
            ->select(['order_no', 'blind_type'])
            ->groupBy('blind_type')->get();

        return response()->json($products);
    }
}
