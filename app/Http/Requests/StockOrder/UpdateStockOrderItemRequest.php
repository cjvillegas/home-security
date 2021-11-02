<?php

namespace App\Http\Requests\StockOrder;

use App\Models\StockLevel;
use App\Models\StockOrder\StockOrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockOrderItemRequest extends FormRequest
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
            'stock_order_id' => 'required|integer|exists:stock_orders,id',
            'stock_level_id' => 'required|integer|exists:stock_levels,id',
            'order_qty' => "required|integer|min:0",
            'status' => [
                'present',
                Rule::in(StockOrderItem::ORDER_STATUSES)
            ],
        ];
    }

    /**
     * Get the stock level instance
     *
     * @return StockLevel
     */
    public function getStockLevel(): StockLevel
    {
        return StockLevel::find($this->request->get('stock_level_id'));
    }
}
