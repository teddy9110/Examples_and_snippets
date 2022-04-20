<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateExpiriesRequest extends FormRequest
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
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.next_payment_date' => 'required|date_format:Y-m-d',
            'users.*.expiry_date' => 'required|date_format:Y-m-d',
        ];
    }
}
