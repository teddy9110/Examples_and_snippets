<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedalsRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'sometimes',
            'per_page' => 'sometimes',
            'group_by' => 'sometimes|in:week,month',
            'start_date' => 'sometimes|date|not_after_if_set:end_date',
            'end_date' => 'sometimes|date|not_before_if_set:start_date',
            'sort_direction' => 'sometimes|in:asc,desc'
        ];
    }
}
