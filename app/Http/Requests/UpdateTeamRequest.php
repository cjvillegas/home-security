<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('team_update');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                Rule::unique('teams')->ignore($this->id)->whereNull('deleted_at')
            ],
            'target' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'folder_names' => 'nullable|array'
        ];
    }
}
