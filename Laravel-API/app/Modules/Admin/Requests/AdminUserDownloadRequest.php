<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;

class AdminUserDownloadRequest extends Request
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
            'user_id' => 'exists:users,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'types' => [
                'required',
                'array',
                'min:1',
                Rule::in([
                    'carbohydrates',
                    'exercise',
                    'fiber',
                    'fat',
                    'protein',
                    'steps',
                    'water',
                    'weight',
                ]),
            ]
        ];
    }
}
