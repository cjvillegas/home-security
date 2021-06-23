<?php

namespace App\Http\Requests;

use App\Models\Process;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateProcessRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('process_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
            'barcode' => [
                'string',
                'required',
                'unique:processes,barcode,' . request()->route('process')->id,
            ],
        ];
    }
}
