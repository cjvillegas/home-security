@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.orderhistory.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.orderhistories.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="order_number_id">{{ trans('cruds.orderhistory.fields.order_number') }}</label>
                <select class="form-control select2 {{ $errors->has('order_number') ? 'is-invalid' : '' }}" name="order_number_id" id="order_number_id">
                    @foreach($order_numbers as $id => $order_number)
                        <option value="{{ $id }}" {{ old('order_number_id') == $id ? 'selected' : '' }}>{{ $order_number }}</option>
                    @endforeach
                </select>
                @if($errors->has('order_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.orderhistory.fields.order_number_helper') }}</span>
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