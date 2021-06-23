@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.scanner.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.scanners.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.scanner.fields.id') }}
                        </th>
                        <td>
                            {{ $scanner->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.scanner.fields.scannedtime') }}
                        </th>
                        <td>
                            {{ $scanner->scannedtime }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.scanner.fields.employeeid') }}
                        </th>
                        <td>
                            {{ $scanner->employeeid }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.scanner.fields.processid') }}
                        </th>
                        <td>
                            {{ $scanner->processid }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.scanner.fields.blindid') }}
                        </th>
                        <td>
                            {{ $scanner->blindid }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.scanners.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection