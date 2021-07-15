@extends('layouts.admin')
@section('content')
@can('process_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.processes.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.process.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Process', 'route' => 'admin.processes.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.process.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Process">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.process.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.process.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.process.fields.barcode') }}
                        </th>
                        <th>Process Categories</th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processes as $key => $process)
                        <tr data-entry-id="{{ $process->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $process->id ?? '' }}
                            </td>
                            <td>
                                {{ $process->name ?? '' }}
                            </td>
                            <td>
                                {{ $process->barcode ?? '' }}
                            </td>
                            <td>
                                @foreach($process->processCategories as $processCategory)
                                    <span class="badge badge-info">{{ ucwords($processCategory->name) }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('process_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.processes.show', $process->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('process_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.processes.edit', $process->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                <button
                                    class="btn btn-xs btn-info"
                                    id="print-barcode"
                                    onclick="printBarcode({{ $process }})">
                                    {{ trans('global.print_barcode') }}
                                </button>

                                @can('process_delete')
                                    <form action="{{ route('admin.processes.destroy', $process->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
        function printBarcode(process) {
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
            content += `<div class="text-uppercase f-size-14">${process.name}</div>`
            content += `<svg id="barcode"></svg>`
            content += "</body></head></html>"

            let script = document.createElement("script")
            script.type = "text/javascript"
            script.src = "{{ asset('js/jsbarcode.code128.min.js') }}"

            let anotherScript = document.createElement("script")
            anotherScript.text += `setTimeout(_ => {
                JsBarcode("#barcode", "${process.barcode}", {
                    height: 35,
                    fontSize: 14
                })
            }, 200)`

            let win = window.open("")
            win.document.write(content)
            win.document.body.appendChild(script)
            win.document.body.appendChild(anotherScript)
            win.document.close()
            setTimeout(_ => {
                win.print()
            }, 300)
        }

        $(function () {
            if ($.fn.dataTable) {
                let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
                @can('process_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.processes.massDestroy') }}",
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
                                .done(function () {
                                    location.reload()
                                })
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

                let table = $('.datatable-Process:not(.ajaxTable)').DataTable({ buttons: dtButtons })
                $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                    $($.fn.dataTable.tables(true)).DataTable()
                        .columns.adjust();
                });
            }
        })
    </script>
@endsection
