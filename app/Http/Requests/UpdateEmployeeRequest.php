<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('employee_edit');
    }

    public function rules()
    {
        return [
            'fullname' => [
                'string',
                'nullable',
            ],
            'barcode' => [
                'string',
                'required',
                'unique:employees,barcode,' . request()->route('employee')->id,
            ],
            'target' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'standard_working_hours' => [
                'required',
                'numeric',
                'min:-2147483648',
                'max:2147483647',
            ],
            'clock_num' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
                Rule::unique('employees')->ignore(request()->route('employee')->id),
            ],
            'shift_id' => [
                'required',
                'integer',
            ],
            'team_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
