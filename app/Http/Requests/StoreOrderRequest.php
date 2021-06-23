<?php

namespace App\Http\Requests;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('order_create');
    }

    public function rules()
    {
        return [
            'order_no' => [
                'string',
                'nullable',
            ],
            'customer' => [
                'string',
                'nullable',
            ],
            'cust_ord_ref' => [
                'string',
                'nullable',
            ],
            'cust_ord_no' => [
                'string',
                'nullable',
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'blind_type' => [
                'string',
                'nullable',
            ],
            'range' => [
                'string',
                'nullable',
            ],
            'colour' => [
                'string',
                'nullable',
            ],
            'stock_code' => [
                'string',
                'nullable',
            ],
            'man_width' => [
                'string',
                'nullable',
            ],
            'man_drop' => [
                'string',
                'nullable',
            ],
            'blind_status' => [
                'string',
                'nullable',
            ],
            'despatch_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'ordered' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'required' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'scheduled_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'roller_table' => [
                'string',
                'nullable',
            ],
            'remake' => [
                'string',
                'nullable',
            ],
            'same_day_despatch' => [
                'string',
                'nullable',
            ],
            'over_size' => [
                'string',
                'nullable',
            ],
            'man_location' => [
                'string',
                'nullable',
            ],
            'order_entered_by' => [
                'string',
                'nullable',
            ],
        ];
    }
}
