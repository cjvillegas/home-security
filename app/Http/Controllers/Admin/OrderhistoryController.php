<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyOrderhistoryRequest;
use App\Http\Requests\StoreOrderhistoryRequest;
use App\Http\Requests\UpdateOrderhistoryRequest;
use App\Models\Order;
use App\Models\Orderhistory;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class OrderhistoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('orderhistory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderhistories = Orderhistory::with(['order_number'])->get();

        return view('admin.orderhistories.index', compact('orderhistories'));
    }

    public function create()
    {
        abort_if(Gate::denies('orderhistory_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order_numbers = Order::all()->pluck('order_no', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.orderhistories.create', compact('order_numbers'));
    }

    public function store(StoreOrderhistoryRequest $request)
    {
        $orderhistory = Orderhistory::create($request->all());

        return redirect()->route('admin.orderhistories.index');
    }

    public function edit(Orderhistory $orderhistory)
    {
        abort_if(Gate::denies('orderhistory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $order_numbers = Order::all()->pluck('order_no', 'id')->prepend(trans('global.pleaseSelect'), '');

        $orderhistory->load('order_number');

        return view('admin.orderhistories.edit', compact('order_numbers', 'orderhistory'));
    }

    public function update(UpdateOrderhistoryRequest $request, Orderhistory $orderhistory)
    {
        $orderhistory->update($request->all());

        return redirect()->route('admin.orderhistories.index');
    }

    public function show(Orderhistory $orderhistory)
    {
        abort_if(Gate::denies('orderhistory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderhistory->load('order_number');

        return view('admin.orderhistories.show', compact('orderhistory'));
    }

    public function destroy(Orderhistory $orderhistory)
    {
        abort_if(Gate::denies('orderhistory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderhistory->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrderhistoryRequest $request)
    {
        Orderhistory::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
