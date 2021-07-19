@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.process.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.processes.update", [$process->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="name">{{ trans('cruds.process.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $process->name) }}">
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.process.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="barcode">{{ trans('cruds.process.fields.barcode') }}</label>
                <input class="form-control {{ $errors->has('barcode') ? 'is-invalid' : '' }}" type="text" name="barcode" id="barcode" value="{{ old('barcode', $process->barcode) }}" required>
                @if($errors->has('barcode'))
                    <div class="invalid-feedback">
                        {{ $errors->first('barcode') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.process.fields.barcode_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="process-categories">Process Categories</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select
                    class="form-control select2 {{ $errors->has('process_categories') ? 'is-invalid' : '' }}"
                    name="process_categories[]"
                    id="process-categories"
                    multiple>
                    @foreach($processCategories as $id => $processCategory)
                        <option
                            value="{{ $processCategory->id }}"
                            {{ (in_array($id, old('process_categories', [])) || $process->processCategories->contains($processCategory->id)) ? 'selected' : '' }}>
                            {{ ucwords($processCategory->name) }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('process_categories'))
                    <div class="invalid-feedback">
                        {{ $errors->first('process_categories') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.role.fields.permissions_helper') }}</span>
            </div>


            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
