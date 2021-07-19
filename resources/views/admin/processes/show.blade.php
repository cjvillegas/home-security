@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.process.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.processes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.process.fields.id') }}
                        </th>
                        <td>
                            {{ $process->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.process.fields.name') }}
                        </th>
                        <td>
                            {{ $process->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.process.fields.barcode') }}
                        </th>
                        <td>
                            {{ $process->barcode }}
                        </td>
                    </tr>
                    <tr>
                        <th>Process Categories</th>
                        <td>
                            @foreach($process->processCategories as $processCategory)
                                <span class="badge badge-info">{{ ucwords($processCategory->name) }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.processes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
