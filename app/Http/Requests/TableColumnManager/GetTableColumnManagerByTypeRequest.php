<?php

namespace App\Http\Requests\TableColumnManager;

use App\Models\TableColumnManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetTableColumnManagerByTypeRequest extends FormRequest
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
            'type' => [
                'required',
                'integer',
                Rule::in(TableColumnManager::COLUMN_TYPES)
            ]
        ];
    }
}
