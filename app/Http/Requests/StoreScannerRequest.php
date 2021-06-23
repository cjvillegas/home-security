<?php

namespace App\Http\Requests;

use App\Models\Scanner;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreScannerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('scanner_create');
    }

    public function rules()
    {
        return [];
    }
}
