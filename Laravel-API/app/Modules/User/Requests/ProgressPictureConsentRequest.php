<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class ProgressPictureConsentRequest extends Request
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
            'type' => 'required|in:accepted,rejected'
        ];
    }

    /** @inheritDoc */
    public function all($keys = null)
    {
        $data = parent::all();
        $data['type'] = $this->route('type');

        return $data;
    }
}
