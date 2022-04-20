<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminMockRequest extends FormRequest
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
            'from' => 'required|date',
            'add' => 'required|integer',
            'period' => 'required|in:weeks,months',
            'type' => 'sometimes|string',
            'medal-type' => 'sometimes|in:gold,silver,bronze'
        ];
    }
}
