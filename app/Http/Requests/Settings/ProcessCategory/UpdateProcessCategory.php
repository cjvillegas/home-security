<?php

namespace App\Http\Requests\Settings\ProcessCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProcessCategory extends FormRequest
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
        if (!Gate::allows('process_categories_edit')) {
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
            'code' => [
                'required',
                'string',
                Rule::unique('process_categories')->ignore($this->id)
            ],
            'name' => 'string|required'
        ];
    }
}
