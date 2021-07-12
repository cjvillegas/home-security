<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Repositories\Orders\OrderRepository;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

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

    public function index()
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.index');
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


    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.create');
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::create($request->all());

        return redirect()->route('admin.orders.index');
    }

    public function edit(Order $order)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.edit', compact('order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderRequest $request)
    {
        Order::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
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
        $page = $request->get('page');
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
        return response()->json($this->repository->searchOrdersByField($request->get('field'), $request->get('searchString')));
    }

    /**
     * Retrieve orders with same order_no
     *
     * @param string $order_no
     *
     * @return JsonResponse
     */
    public function showOrderList($order_no): JsonResponse
    {
        $orders= Order::where('order_no', $order_no)
            ->with(['scanners' => function ($query) {
                $query->with(['employee' => function ($query) {
                    $query->with(['shift', 'team']);
                }, 'process']);
            }])
            ->get();

        return response()->json($orders);
    }
}
