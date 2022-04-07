<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonitoringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->get('id');

        return [
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('monitorings')->where(function ($query) use ($id) {
                    $query->where('id', '!=', $id)
                        ->whereNull('deleted_at');
                })
            ],
            'ip_address' => 'nullable|ip'
        ];
    }
}
