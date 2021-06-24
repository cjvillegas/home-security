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
                        {{-- Column for checkbox --}}
                        <th width="10"></th>
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
                        <th></th>
                    </tr>
                </thead>
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
                        // fetch the second column's ID text
                        return $(entry).find("td:eq(1)").text()
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
                            data: { ids: ids, _method: 'DELETE' }
                        })
                        .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
        @endcan

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [[ 1, 'desc' ]],
            pageLength: 25,
        });

        let table = $('.datatable-Scanner:not(.ajaxTable)').DataTable({
            searchDelay: 500,
            buttons: dtButtons,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/scanners/fetch-scanners',
            },
            columns: [
                {
                    data: 'is_checked',
                    name: 'is_checked'
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'scannedtime',
                    name: 'scannedtime'
                },
                {
                    data: 'employeeid',
                    name: 'employeeid'
                },
                {
                    data: 'processid',
                    name: 'processid'
                },
                {
                    data: 'blindid',
                    name: 'blindid'
                },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        // build the action column
                        let elements = ''
                        // for viewing
{{--                        @can('scanner_show')--}}
{{--                            elements += `<a class="btn btn-xs btn-primary" href="/admin/scanners/${data}">{{ trans('global.view') }}</a>`--}}
{{--                        @endcan--}}

                        @can('scanner_edit')
                            elements += `<a class="btn btn-xs btn-info" href="/admin/scanners/${data}/edit">
                                {{ trans('global.edit') }}
                            </a>`
                        @endcan

                        @can('scanner_delete')
                            elements += `<form
                                class="d-inline-block ml-3"
                                action="/admin/scanners/${data}"
                                method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                            </form>`
                        @endcan

                        return elements
                    }
                }
            ],

        })
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
        });
})

</script>
@endsection
