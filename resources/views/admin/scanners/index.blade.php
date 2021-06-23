@extends('layouts.admin')
@section('content')
@can('scanner_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.scanners.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.scanner.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Scanner', 'route' => 'admin.scanners.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.scanner.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Scanner">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.scanner.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.scanner.fields.scannedtime') }}
                        </th>
                        <th>
                            {{ trans('cruds.scanner.fields.employeeid') }}
                        </th>
                        <th>
                            {{ trans('cruds.scanner.fields.processid') }}
                        </th>
                        <th>
                            {{ trans('cruds.scanner.fields.blindid') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scanners as $key => $scanner)
                        <tr data-entry-id="{{ $scanner->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $scanner->id ?? '' }}
                            </td>
                            <td>
                                {{ $scanner->scannedtime ?? '' }}
                            </td>
                            <td>
                                {{ $scanner->employeeid ?? '' }}
                            </td>
                            <td>
                                {{ $scanner->processid ?? '' }}
                            </td>
                            <td>
                                {{ $scanner->blindid ?? '' }}
                            </td>
                            <td>
                                @can('scanner_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.scanners.show', $scanner->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('scanner_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.scanners.edit', $scanner->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('scanner_delete')
                                    <form action="{{ route('admin.scanners.destroy', $scanner->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('scanner_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.scanners.massDestroy') }}",
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

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Scanner:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection