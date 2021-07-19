<?php

namespace App\Http\Requests\Settings\ProcessCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProcessCategory extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // checks if the current user has the authorization to create a
        // new process category
        if (!Gate::allows('process_categories_create')) {
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
            'code' => 'string|required|unique:process_categories',
            'name' => 'string|required'
        ];
    }
}
