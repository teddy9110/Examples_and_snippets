<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDirectDebitSignupsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'sometimes|numeric',
            'page_size' => 'sometimes|numeric',
            'filter' => 'sometimes',
        ];
    }
}
