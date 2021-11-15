<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
            ],
            'team_trade_target' => [
                'integer',
                'required',
                'min:1'
            ],
            'team_internet_target' => [
                'integer',
                'required',
                'min:1'
            ]
        ];
    }
}
