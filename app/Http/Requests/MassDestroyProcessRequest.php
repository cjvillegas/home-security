<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Process;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyProcessRequest extends FormRequest  {





public function authorize()
{
    abort_if(Gate::denies('process_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');




return true;
    
}
public function rules()
{
    



return [
'ids' => 'required|array',
    'ids.*' => 'exists:processes,id',
]
    
}

}