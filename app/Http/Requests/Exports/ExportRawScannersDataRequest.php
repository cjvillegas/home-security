<?php

namespace App\Http\Requests\Exports;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ExportRawScannersDataRequest extends FormRequest
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
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = new Carbon($this->start);
            $end = new Carbon($this->end);

            $diff = $end->diff($start)->days;

            // check if selected dates are more than 31 days, if so prevent exporting data
            if (abs($diff) > 31) {
                $validator->errors()->add('start', 'Dates selected should not be greater than 31 days.');
            }
        });
    }
}
