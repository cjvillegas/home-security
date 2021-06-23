<?php

namespace App\Http\Requests;

use App\Models\Orderhistory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOrderhistoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('orderhistory_edit');
    }

    public function rules()
    {
        return [];
    }
}
