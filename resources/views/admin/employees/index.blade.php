@extends('layouts.admin')
@section('content')
@can('employee_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.employees.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.employee.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Employee', 'route' => 'admin.employees.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.employee.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Employee">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.fullname') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.barcode') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.pin_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.target') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.shift') }}
                        </th>
                        <th>
                            {{ trans('cruds.employee.fields.team') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $key => $employee)
                        <tr data-entry-id="{{ $employee->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $employee->id ?? '' }}
                            </td>
                            <td>
                                {{ $employee->fullname ?? '' }}
                            </td>
                            <td>
                                {{ $employee->barcode ?? '' }}
                            </td>
                            <td>
                                {{ $employee->pin_code ?? '' }}
                            </td>
                            <td>
                                {{ $employee->target ?? '' }}
                            </td>
                            <td>
                                {{ $employee->shift->name ?? '' }}
                            </td>
                            <td>
                                {{ $employee->team->name ?? '' }}
                            </td>
                            <td>
                                @can('employee_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.employees.show', $employee->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('employee_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.employees.edit', $employee->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                <button
                                    class="btn btn-xs btn-info"
                                    id="print-barcode"
                                    onclick="fuck({{ $employee }})">
                                    {{ trans('global.print_barcode') }}
                                </button>

                                @can('employee_delete')
                                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    function fuck(employee) {
        let win = window.open("")

        let content = "<html><head>"
        content += `<link href="{{ asset('css/print.css') }}" rel="stylesheet" />`
        content += `<style media="print">
            @page
            {
                margin: 0mm;  /* this affects the margin in the printer settings */
                size: 89mm 28mm;
            }
        </style>`
        content += "<body class='text-center'>"
        content += `<div>${employee.fullname}</div>`
        content += `<div class="code-font">${employee.barcode}</div>`
        content += "</body></head></html>"

        win.document.write(content)
        win.document.close()
        setTimeout(_ => {
            win.print()
        }, 200)
    }

    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('employee_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.employees.massDestroy') }}",
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
            pageLength: 100,
        });

        let table = $('.datatable-Employee:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
        });


    })
</script>
@endsection
