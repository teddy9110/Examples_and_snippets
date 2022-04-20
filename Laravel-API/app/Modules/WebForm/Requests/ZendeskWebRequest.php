<?php

namespace Rhf\Modules\WebForm\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZendeskWebRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
            'subject' => 'required',
            'message' => 'required|string|max:550|min:10',
            'attachments.*' => 'sometimes|mimes:jpeg,jpg,png,webp,pdf'
        ];
    }
}
