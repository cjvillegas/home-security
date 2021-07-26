<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class MachineCounterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('machine_counter_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'machine_id' => 'required',
            'employee_id' => 'required',
            'shift_id' => 'required',
            'start_counter' => 'numeric|nullable',
            'start_counter_time' => 'required',
            'stop_counter' => 'numeric|nullable',
            'stop_counter_time' => 'required'
        ];
    }
}
