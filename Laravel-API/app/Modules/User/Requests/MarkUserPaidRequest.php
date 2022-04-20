<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkUserPaidRequest extends FormRequest
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
            'next_payment_date' => 'sometimes|nullable|date_format:Y-m-d',
            'expiry_date' => 'sometimes|date_format:Y-m-d',
        ];
    }
}
