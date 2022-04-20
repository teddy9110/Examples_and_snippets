<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;

class AdminCreateSubscriptionRequest extends Request
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
            'user_id' => 'required|exists:users,id',
            'subscription_provider' => [
                'required',
                'string',
                Rule::in(['free']),
            ],
            'subscription_frequency'  => [
                'required',
                'string',
                Rule::in(['monthly']),
            ],
            'expiry_date'  => [
                'required',
                'date_format:Y-m-d'
            ],
        ];
    }
}
