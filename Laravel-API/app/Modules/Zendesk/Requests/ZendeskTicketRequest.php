<?php

namespace Rhf\Modules\Zendesk\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZendeskTicketRequest extends FormRequest
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
        switch ($this->route()->getName()) {
            case 'update':
                return [
                    'comment' => 'required|string|min:10|max:1000',
                    'files.*' => 'sometimes|mimes:jpeg,jpg,png,webp,pdf'
                ];
            case 'create':
                return [
                    'comment' => 'required|string|min:10|max:1000',
                    'tags' => 'required|array|min:1',
                    'platform' => ['required', 'regex:/^(ios|android)$/i'],
                    'app_version' => 'required',
                    'files.*' => 'sometimes|mimes:jpeg,jpg,png,webp,pdf'
                ];
        }
    }
}
