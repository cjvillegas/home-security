<?php

namespace App\Http\Requests\StockOrder;

use Illuminate\Foundation\Http\FormRequest;

class MoveStockOrderItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'order_id' => 'required|integer|exists:stock_orders,id',
            'order_item_ids' => 'required|array',
            'order_item_ids.*' => 'required|integer|exists:stock_order_items,id'
        ];
    }
}
