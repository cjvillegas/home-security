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
            'trade_target' => [
                'numeric',
                'nullable'
            ],
            'trade_target_new_joiner' => [
                'numeric',
                'nullable'
            ],
            'internet_target' => [
                'numeric',
                'nullable'
            ],
            'internet_target_new_joiner' => [
                'numeric',
                'nullable'
            ]
        ];
    }
}
