@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Order Details
    </div>

<style>
    .datatable {
    width: 200% !important;
}
</style>
        <div class="card-body">
            <h3>Order No <strong>#{{ $order->order_no ?? '' }}</strong></h3><br/>
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Order">
                    <thead>
                        <tr>
                            <th>
                                {{ trans('cruds.order.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.blindid') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.order_no') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.customer') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.cust_ord_ref') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.cust_ord_no') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.quantity') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.blind_type') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.range') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.colour') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.stock_code') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.man_width') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.man_drop') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.blind_status') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.despatch_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.ordered') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.required') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.scheduled_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.roller_table') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.remake') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.same_day_despatch') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.over_size') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.man_location') }}
                            </th>
                            <th>
                                {{ trans('cruds.order.fields.order_entered_by') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
        @foreach($orders as $key => $order)

        <tr>

            <td>
                {{ $order->id ?? '' }}
            </td>
            <td>
             {{ $order->blindid ?? '' }}
            </td>
            <td>
             {{ $order->order_no ?? '' }}
            </td>
            <td>
                {{ $order->customer ?? '' }}
            </td>
            <td>
                {{ $order->cust_ord_ref ?? '' }}
            </td>
            <td>
                {{ $order->cust_ord_no ?? '' }}
            </td>
            <td>
                {{ $order->quantity ?? '' }}
            </td>
            <td>
                {{ $order->blind_type ?? '' }}
            </td>
            <td>
                {{ $order->range ?? '' }}
            </td>
            <td>
                {{ $order->colour ?? '' }}
            </td>
            <td>
                {{ $order->stock_code ?? '' }}
            </td>
            <td>
                {{ $order->man_width ?? '' }}
            </td>
            <td>
                {{ $order->man_drop ?? '' }}
            </td>
            <td>
                {{ $order->blind_status ?? '' }}
            </td>
            <td>
                {{ $order->despatch_date ?? '' }}
            </td>
            <td>
                {{ $order->ordered ?? '' }}
            </td>
            <td>
                {{ $order->required ?? '' }}
            </td>
            <td>
                {{ $order->scheduled_date ?? '' }}
            </td>
            <td>
                {{ $order->roller_table ?? '' }}
            </td>
            <td>
                {{ $order->remake ?? '' }}
            </td>
            <td>
                {{ $order->same_day_despatch ?? '' }}
            </td>
            <td>
                {{ $order->over_size ?? '' }}
            </td>
            <td>
                {{ $order->man_location ?? '' }}
            </td>
            <td>
                {{ $order->order_entered_by ?? '' }}
            </td>


        </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div><div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
        {!! $orders->links() !!}

    </div>
</div>

        <div class="card">
            <div class="card-header">
                Manufacturing History
            </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable-Order">
                    <thead>
                        <tr>
                            <th>
                                Full Name
                            </th>
                            <th>
                                Scanned Time
                            </th>
                            <th>
                                Process
                            </th>
                            <th>
                                Blind ID
                            </th>
                            <th>
                               Order No
                            </th>
                            <th>
                                Customer
                            </th>
                            <th>
                                CustOrdRef
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                      @if($ordersem->isNotEmpty())
        @foreach($ordersem as $key => $orderm)

        <tr>
            <td>
                {{ $orderm->fullname ?? '' }}
            </td>
            <td>
                {{ $orderm->scannedtime ?? '' }}
            </td>
            <td>
                {{ $orderm->name ?? '' }}
            </td>
            <td>
                {{ $orderm->blindid ?? '' }}
            </td>
            <td>
                {{ $orderm->order_no ?? '' }}
            </td>
            <td>
                {{ $orderm->customer ?? '' }}
            </td>
            <td>
                {{ $orderm->cust_ord_ref ?? '' }}
            </td>

        </tr>
        @endforeach
        @else
          <td colspan="7" class="text-center">
            No data available in table

      </td>
        @endif
        </tbody>
    </table>
</div>
</div>
@endsection


