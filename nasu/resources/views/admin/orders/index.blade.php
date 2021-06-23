@extends('layouts.admin')
@section('content')
@can('order_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.orders.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.order.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Order', 'route' => 'admin.orders.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.order.title_singular') }} {{ trans('global.list') }}
    </div>

    <style>
        .datatable {
        width: 200% !important;
    }
    </style>
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Order">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
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
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $key => $order)
                        <tr data-entry-id="{{ $order->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $order->id ?? '' }}
                            </td>
                            <td>
                                {{ $order->blindid ?? '' }}
                            </td>
                            <td>
                                <a href="{{ url('admin/orders/vieworderno', $order->order_no) }}"> {{ $order->order_no ?? '' }}</a>
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
                            <td>
                                @can('order_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.orders.show', $order->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('order_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.orders.edit', $order->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('order_delete')
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
{!! $orders->links() !!}
</div>


@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('order_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.orders.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan
/*
  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Order:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

})
*/
</script>
@endsection
