<?php

namespace App\Http\Requests;

use App\Models\Shift;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateShiftRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shift_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                Rule::unique('shifts')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }
}
