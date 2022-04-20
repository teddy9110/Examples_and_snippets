<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class GetUsersByEmailRequest extends Request
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
            'emails' => 'required|array',
        ];
    }
}
