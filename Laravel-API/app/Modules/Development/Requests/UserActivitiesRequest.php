<?php

namespace Rhf\Modules\Development\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;

class UserActivitiesRequest extends Request
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
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date',
            'user_id' => 'required',
            'types' => [
                'sometimes',
                'array',
                Rule::in(['steps', 'calories', 'fiber', 'fat', 'protein', 'water', 'weight', 'carbohydrates'])
            ]
        ];
    }
}
