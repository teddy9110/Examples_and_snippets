<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Modules\User\Enums\Gender;
use Rhf\Modules\User\Enums\WeightUnit;

class AdminUserRequest extends Request
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
        return
            [
            'per_page' => [
                'sometimes',
                'number'
            ],
            'order_by' => [
                'somtimes',
                'string'
            ],
            'order_direction' => [
                'sometimes',
                'in:asc,desc',
            ],
            'filter_by' => [
                'sometimes',
                'string'
            ],
            'filter_account_type' => [
                'sometimes',
                'in:staff,customer,all',
            ]
        ];
    }
}
