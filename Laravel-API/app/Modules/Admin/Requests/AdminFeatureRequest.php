<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminFeatureRequest extends FormRequest
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
            'value' => 'required|boolean',
        ];
    }
}
