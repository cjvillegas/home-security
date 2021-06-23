<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Orderhistory;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyOrderhistoryRequest extends FormRequest  {





public function authorize()
{
    abort_if(Gate::denies('orderhistory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');




return true;
    
}
public function rules()
{
    



return [
'ids' => 'required|array',
    'ids.*' => 'exists:orderhistories,id',
]
    
}

}