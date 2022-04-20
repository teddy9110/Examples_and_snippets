<?php

namespace Rhf\Modules\System\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Enums\Platforms;

class AppVersionRequest extends Request
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

    public function validationData()
    {
        return array_merge($this->request->all(), [
            'platform' => $this->route('platform'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.

     * @return array
     */
    public function rules()
    {
        return [
            'platform' => [
                'required',
                Rule::in(Platforms::getValues())
            ],
        ];
    }
}
