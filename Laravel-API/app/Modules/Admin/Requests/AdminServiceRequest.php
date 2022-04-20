<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Enums\ServiceStatus;

class AdminServiceRequest extends Request
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

     * @return array
     */
    public function rules()
    {
        return [
            'status' => [
                'required',
                Rule::in(ServiceStatus::getValues())
            ]
        ];
    }
}
