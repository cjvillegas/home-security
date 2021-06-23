<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderhistoryRequest;
use App\Http\Requests\UpdateOrderhistoryRequest;
use App\Http\Resources\Admin\OrderhistoryResource;
use App\Models\Orderhistory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderhistoryApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('orderhistory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderhistoryResource(Orderhistory::with(['order_number'])->get());
    }

    public function store(StoreOrderhistoryRequest $request)
    {
        $orderhistory = Orderhistory::create($request->all());

        return (new OrderhistoryResource($orderhistory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Orderhistory $orderhistory)
    {
        abort_if(Gate::denies('orderhistory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new OrderhistoryResource($orderhistory->load(['order_number']));
    }

    public function update(UpdateOrderhistoryRequest $request, Orderhistory $orderhistory)
    {
        $orderhistory->update($request->all());

        return (new OrderhistoryResource($orderhistory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Orderhistory $orderhistory)
    {
        abort_if(Gate::denies('orderhistory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderhistory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
