<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('employee_create');
    }

    public function rules()
    {
        return [
            'fullname' => [
                'string',
                'nullable',
            ],
            'pin_code' => [
                'string',
                'required',
                'unique:employees',
            ],
            'target' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'standard_working_hours' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'clock_num' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
                Rule::unique('employees'),
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
