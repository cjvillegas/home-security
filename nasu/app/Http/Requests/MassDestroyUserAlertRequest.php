<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserAlert;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyUserAlertRequest extends FormRequest  {





public function authorize()
{
    abort_if(Gate::denies('user_alert_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');




return true;
    
}
public function rules()
{
    



return [
'ids' => 'required|array',
    'ids.*' => 'exists:user_alerts,id',
]
    
}

}