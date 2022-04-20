<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDirectDebitWelcomeEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|string|regex:/(.+)@(.+)\.(.+)/i',
            'reference' => 'required|string',
        ];
    }
}
