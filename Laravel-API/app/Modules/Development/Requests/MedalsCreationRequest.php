<?php

namespace Rhf\Modules\Development\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Rhf\Modules\Development\Rules\MedalCreationRule;

class MedalsCreationRequest extends Request
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
            'medals' => 'required',
            'medals.*' => new MedalCreationRule(),
        ];
    }
}
