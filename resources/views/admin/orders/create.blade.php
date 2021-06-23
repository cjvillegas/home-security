@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.order.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.orders.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="order_no">{{ trans('cruds.order.fields.order_no') }}</label>
                <input class="form-control {{ $errors->has('order_no') ? 'is-invalid' : '' }}" type="text" name="order_no" id="order_no" value="{{ old('order_no', '') }}">
                @if($errors->has('order_no'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_no') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.order_no_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="customer">{{ trans('cruds.order.fields.customer') }}</label>
                <input class="form-control {{ $errors->has('customer') ? 'is-invalid' : '' }}" type="text" name="customer" id="customer" value="{{ old('customer', '') }}">
                @if($errors->has('customer'))
                    <div class="invalid-feedback">
                        {{ $errors->first('customer') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.customer_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cust_ord_ref">{{ trans('cruds.order.fields.cust_ord_ref') }}</label>
                <input class="form-control {{ $errors->has('cust_ord_ref') ? 'is-invalid' : '' }}" type="text" name="cust_ord_ref" id="cust_ord_ref" value="{{ old('cust_ord_ref', '') }}">
                @if($errors->has('cust_ord_ref'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cust_ord_ref') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.cust_ord_ref_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="cust_ord_no">{{ trans('cruds.order.fields.cust_ord_no') }}</label>
                <input class="form-control {{ $errors->has('cust_ord_no') ? 'is-invalid' : '' }}" type="text" name="cust_ord_no" id="cust_ord_no" value="{{ old('cust_ord_no', '') }}">
                @if($errors->has('cust_ord_no'))
                    <div class="invalid-feedback">
                        {{ $errors->first('cust_ord_no') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.cust_ord_no_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="quantity">{{ trans('cruds.order.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', '') }}" step="1">
                @if($errors->has('quantity'))
                    <div class="invalid-feedback">
                        {{ $errors->first('quantity') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="blind_type">{{ trans('cruds.order.fields.blind_type') }}</label>
                <input class="form-control {{ $errors->has('blind_type') ? 'is-invalid' : '' }}" type="text" name="blind_type" id="blind_type" value="{{ old('blind_type', '') }}">
                @if($errors->has('blind_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('blind_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.blind_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="range">{{ trans('cruds.order.fields.range') }}</label>
                <input class="form-control {{ $errors->has('range') ? 'is-invalid' : '' }}" type="text" name="range" id="range" value="{{ old('range', '') }}">
                @if($errors->has('range'))
                    <div class="invalid-feedback">
                        {{ $errors->first('range') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.range_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="colour">{{ trans('cruds.order.fields.colour') }}</label>
                <input class="form-control {{ $errors->has('colour') ? 'is-invalid' : '' }}" type="text" name="colour" id="colour" value="{{ old('colour', '') }}">
                @if($errors->has('colour'))
                    <div class="invalid-feedback">
                        {{ $errors->first('colour') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.colour_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="stock_code">{{ trans('cruds.order.fields.stock_code') }}</label>
                <input class="form-control {{ $errors->has('stock_code') ? 'is-invalid' : '' }}" type="text" name="stock_code" id="stock_code" value="{{ old('stock_code', '') }}">
                @if($errors->has('stock_code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('stock_code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.stock_code_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="man_width">{{ trans('cruds.order.fields.man_width') }}</label>
                <input class="form-control {{ $errors->has('man_width') ? 'is-invalid' : '' }}" type="text" name="man_width" id="man_width" value="{{ old('man_width', '') }}">
                @if($errors->has('man_width'))
                    <div class="invalid-feedback">
                        {{ $errors->first('man_width') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.man_width_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="man_drop">{{ trans('cruds.order.fields.man_drop') }}</label>
                <input class="form-control {{ $errors->has('man_drop') ? 'is-invalid' : '' }}" type="text" name="man_drop" id="man_drop" value="{{ old('man_drop', '') }}">
                @if($errors->has('man_drop'))
                    <div class="invalid-feedback">
                        {{ $errors->first('man_drop') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.man_drop_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="blind_status">{{ trans('cruds.order.fields.blind_status') }}</label>
                <input class="form-control {{ $errors->has('blind_status') ? 'is-invalid' : '' }}" type="text" name="blind_status" id="blind_status" value="{{ old('blind_status', '') }}">
                @if($errors->has('blind_status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('blind_status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.blind_status_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="despatch_date">{{ trans('cruds.order.fields.despatch_date') }}</label>
                <input class="form-control date {{ $errors->has('despatch_date') ? 'is-invalid' : '' }}" type="text" name="despatch_date" id="despatch_date" value="{{ old('despatch_date') }}">
                @if($errors->has('despatch_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('despatch_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.despatch_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="ordered">{{ trans('cruds.order.fields.ordered') }}</label>
                <input class="form-control date {{ $errors->has('ordered') ? 'is-invalid' : '' }}" type="text" name="ordered" id="ordered" value="{{ old('ordered') }}">
                @if($errors->has('ordered'))
                    <div class="invalid-feedback">
                        {{ $errors->first('ordered') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.ordered_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="required">{{ trans('cruds.order.fields.required') }}</label>
                <input class="form-control date {{ $errors->has('required') ? 'is-invalid' : '' }}" type="text" name="required" id="required" value="{{ old('required') }}">
                @if($errors->has('required'))
                    <div class="invalid-feedback">
                        {{ $errors->first('required') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.required_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="scheduled_date">{{ trans('cruds.order.fields.scheduled_date') }}</label>
                <input class="form-control date {{ $errors->has('scheduled_date') ? 'is-invalid' : '' }}" type="text" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}">
                @if($errors->has('scheduled_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('scheduled_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.scheduled_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="roller_table">{{ trans('cruds.order.fields.roller_table') }}</label>
                <input class="form-control {{ $errors->has('roller_table') ? 'is-invalid' : '' }}" type="text" name="roller_table" id="roller_table" value="{{ old('roller_table', '') }}">
                @if($errors->has('roller_table'))
                    <div class="invalid-feedback">
                        {{ $errors->first('roller_table') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.roller_table_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="remake">{{ trans('cruds.order.fields.remake') }}</label>
                <input class="form-control {{ $errors->has('remake') ? 'is-invalid' : '' }}" type="text" name="remake" id="remake" value="{{ old('remake', '') }}">
                @if($errors->has('remake'))
                    <div class="invalid-feedback">
                        {{ $errors->first('remake') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.remake_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="same_day_despatch">{{ trans('cruds.order.fields.same_day_despatch') }}</label>
                <input class="form-control {{ $errors->has('same_day_despatch') ? 'is-invalid' : '' }}" type="text" name="same_day_despatch" id="same_day_despatch" value="{{ old('same_day_despatch', '') }}">
                @if($errors->has('same_day_despatch'))
                    <div class="invalid-feedback">
                        {{ $errors->first('same_day_despatch') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.same_day_despatch_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="over_size">{{ trans('cruds.order.fields.over_size') }}</label>
                <input class="form-control {{ $errors->has('over_size') ? 'is-invalid' : '' }}" type="text" name="over_size" id="over_size" value="{{ old('over_size', '') }}">
                @if($errors->has('over_size'))
                    <div class="invalid-feedback">
                        {{ $errors->first('over_size') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.over_size_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="man_location">{{ trans('cruds.order.fields.man_location') }}</label>
                <input class="form-control {{ $errors->has('man_location') ? 'is-invalid' : '' }}" type="text" name="man_location" id="man_location" value="{{ old('man_location', '') }}">
                @if($errors->has('man_location'))
                    <div class="invalid-feedback">
                        {{ $errors->first('man_location') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.man_location_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="order_entered_by">{{ trans('cruds.order.fields.order_entered_by') }}</label>
                <input class="form-control {{ $errors->has('order_entered_by') ? 'is-invalid' : '' }}" type="text" name="order_entered_by" id="order_entered_by" value="{{ old('order_entered_by', '') }}">
                @if($errors->has('order_entered_by'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_entered_by') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.order.fields.order_entered_by_helper') }}</span>
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