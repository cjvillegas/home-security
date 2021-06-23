<?php

namespace App\Http\Requests;

use App\Models\Process;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreProcessRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('process_create');
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
                'unique:processes',
            ],
        ];
    }
}
