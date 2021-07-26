@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.employee.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.employees.update", [$employee->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="fullname">{{ trans('cruds.employee.fields.fullname') }}</label>
                <input class="form-control {{ $errors->has('fullname') ? 'is-invalid' : '' }}" type="text" name="fullname" id="fullname" value="{{ old('fullname', $employee->fullname) }}">
                @if($errors->has('fullname'))
                    <div class="invalid-feedback">
                        {{ $errors->first('fullname') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.fullname_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="barcode">{{ trans('cruds.employee.fields.barcode') }}</label>
                <input class="form-control {{ $errors->has('barcode') ? 'is-invalid' : '' }}" type="text" name="barcode" id="barcode" value="{{ old('barcode', $employee->barcode) }}" required>
                @if($errors->has('barcode'))
                    <div class="invalid-feedback">
                        {{ $errors->first('barcode') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.barcode_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pin_code">{{ trans('cruds.employee.fields.pin_code') }}</label>
                <input class="form-control {{ $errors->has('pin_code') ? 'is-invalid' : '' }}" type="text" name="pin_code" id="pin_code" value="{{ old('pin_code', $employee->pin_code) }}" required>
                @if($errors->has('pin_code'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pin_code') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.barcode_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="target">{{ trans('cruds.employee.fields.target') }}</label>
                <input class="form-control {{ $errors->has('target') ? 'is-invalid' : '' }}" type="number" name="target" id="target" value="{{ old('target', $employee->target) }}" step="1">
                @if($errors->has('target'))
                    <div class="invalid-feedback">
                        {{ $errors->first('target') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.target_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="target">Working Hours</label>
                <input
                    class="form-control {{ $errors->has('standard_working_hours') ? 'is-invalid' : '' }}"
                    type="number"
                    name="standard_working_hours"
                    id="standard_working_hours"
                    value="{{ old('standard_working_hours', $employee->standard_working_hours) }}"
                    step=".1"
                    required>
                @if($errors->has('standard_working_hours'))
                    <div class="invalid-feedback">
                        {{ $errors->first('standard_working_hours') }}
                    </div>
                @endif
                <span class="help-block">The standard working hours of employees per shift.</span>
            </div>

            <div class="form-group">
                <label for="clock_num">Clock No.</label>
                <input class="form-control {{ $errors->has('clock_num') ? 'is-invalid' : '' }}" type="number" name="clock_num" id="clock_num" value="{{ old('clock_num', $employee->clock_num) }}" step="1">
                @if($errors->has('clock_num'))
                    <div class="invalid-feedback">
                        {{ $errors->first('clock_num') }}
                    </div>
                @endif
                <span class="help-block">An employee's clock number.</span>
            </div>


            <div class="form-group">
                <label class="required" for="shift_id">{{ trans('cruds.employee.fields.shift') }}</label>
                <select class="form-control select2 {{ $errors->has('shift') ? 'is-invalid' : '' }}" name="shift_id" id="shift_id" required>
                    @foreach($shifts as $id => $shift)
                        <option value="{{ $id }}" {{ (old('shift_id') ? old('shift_id') : $employee->shift->id ?? '') == $id ? 'selected' : '' }}>{{ $shift }}</option>
                    @endforeach
                </select>
                @if($errors->has('shift'))
                    <div class="invalid-feedback">
                        {{ $errors->first('shift') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.shift_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="team_id">{{ trans('cruds.employee.fields.team') }}</label>
                <select class="form-control select2 {{ $errors->has('team') ? 'is-invalid' : '' }}" name="team_id" id="team_id" required>
                    @foreach($teams as $id => $team)
                        <option value="{{ $id }}" {{ (old('team_id') ? old('team_id') : $employee->team->id ?? '') == $id ? 'selected' : '' }}>{{ $team }}</option>
                    @endforeach
                </select>
                @if($errors->has('team'))
                    <div class="invalid-feedback">
                        {{ $errors->first('team') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employee.fields.team_helper') }}</span>
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
