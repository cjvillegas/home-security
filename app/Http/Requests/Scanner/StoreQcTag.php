<?php

namespace App\Http\Requests\Scanner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreQcTag extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // checks if the current user has the authorization to create a
        // new qc tag
        if (!Gate::allows('qc_tag_create')) {
            abort(401);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quality_control_id' => 'required|integer|exists:quality_controls,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'user_id' => 'required|integer|exists:users,id',
            'process_id' => 'required|integer|exists:processes,id',
            'scanner_id' => 'required|integer|exists:scanners,id',
            'description' => 'required|string',
            'operation_date' => 'nullable|date',
        ];
    }
}
