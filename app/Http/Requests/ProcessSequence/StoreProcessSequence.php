<?php

namespace App\Http\Requests\ProcessSequence;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessSequence extends FormRequest
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
            'name' => 'string|required|unique:process_sequences',
        ];
    }
}
