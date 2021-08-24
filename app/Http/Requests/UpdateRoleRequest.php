<?php

namespace App\Http\Requests;

use App\Models\Role;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('role_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
                Rule::unique('roles')->ignore($this->id)->whereNull('deleted_at')
            ],
            'permissions.*' => [
                'integer',
            ],
            'permissions' => [
                'required',
                'array',
            ],
        ];
    }
}
