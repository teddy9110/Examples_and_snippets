<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogRequest extends FormRequest
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
            'type' => 'sometimes|in:steps,water,weight,exercise,workout',
            'start_date' => 'sometimes|not_after_if_set:end_date',
            'end_date' => 'sometimes|not_before_if_set:start_date',
            'sort_by' => 'sometimes',
            'sort_direction' => 'sometimes|in:asc,desc',
            'group_by' => 'sometimes|in:week,month',
            'page' => 'sometimes',
            'limit' => 'sometimes',
        ];
    }
}
