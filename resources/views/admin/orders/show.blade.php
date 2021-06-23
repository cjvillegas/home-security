@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.order.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.id') }}
                        </th>
                        <td>
                            {{ $order->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.blindid') }}
                        </th>
                        <td>
                            {{ $order->blindid }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.order_no') }}
                        </th>
                        <td>
                            {{ $order->order_no }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.customer') }}
                        </th>
                        <td>
                            {{ $order->customer }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.cust_ord_ref') }}
                        </th>
                        <td>
                            {{ $order->cust_ord_ref }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.cust_ord_no') }}
                        </th>
                        <td>
                            {{ $order->cust_ord_no }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.quantity') }}
                        </th>
                        <td>
                            {{ $order->quantity }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.blind_type') }}
                        </th>
                        <td>
                            {{ $order->blind_type }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.range') }}
                        </th>
                        <td>
                            {{ $order->range }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.colour') }}
                        </th>
                        <td>
                            {{ $order->colour }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.stock_code') }}
                        </th>
                        <td>
                            {{ $order->stock_code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.man_width') }}
                        </th>
                        <td>
                            {{ $order->man_width }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.man_drop') }}
                        </th>
                        <td>
                            {{ $order->man_drop }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.blind_status') }}
                        </th>
                        <td>
                            {{ $order->blind_status }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.despatch_date') }}
                        </th>
                        <td>
                            {{ $order->despatch_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.ordered') }}
                        </th>
                        <td>
                            {{ $order->ordered }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.required') }}
                        </th>
                        <td>
                            {{ $order->required }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.scheduled_date') }}
                        </th>
                        <td>
                            {{ $order->scheduled_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.roller_table') }}
                        </th>
                        <td>
                            {{ $order->roller_table }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.remake') }}
                        </th>
                        <td>
                            {{ $order->remake }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.same_day_despatch') }}
                        </th>
                        <td>
                            {{ $order->same_day_despatch }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.over_size') }}
                        </th>
                        <td>
                            {{ $order->over_size }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.man_location') }}
                        </th>
                        <td>
                            {{ $order->man_location }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.order.fields.order_entered_by') }}
                        </th>
                        <td>
                            {{ $order->order_entered_by }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.orders.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection