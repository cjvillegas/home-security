<?php

namespace App\Http\Requests;

use App\Models\Team;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
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
        ];
    }
}
