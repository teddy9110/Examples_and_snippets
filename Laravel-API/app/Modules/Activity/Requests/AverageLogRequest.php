<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AverageLogRequest extends FormRequest
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
            'type' => 'sometimes|in:week,month',
            'period' => [
                'sometimes',
                'regex:/[a-zA-Z]+-[1-9]{1}[0-9]*-(months|weeks)/i',
            ],
        ];
    }
}
