<?php

namespace Rhf\Modules\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiNotificationsRequest extends FormRequest
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
        switch ($this->method()) {
            case 'GET':
                return [
                    'platform' => 'required',
                    'app_version' => 'required|regex:/\d+(.)\d+(.)\d+/i',
                    'platform_version' => 'sometimes',
                ];
            case 'POST':
                return [
                    'app_version' => 'required',
                    'platform' => 'required|in:android,ios,all',
                    'platform_version' => 'sometimes|nullable',
                    'title' => 'required',
                    'content' => 'required',
                    'action_text' => 'required',
                    'action_callback' => 'sometimes|nullable',
                    'type' => 'sometimes',
                    'not_before' => 'sometimes|nullable|date_format:Y-m-d',
                    'not_after' => 'sometimes|nullable|date_format:Y-m-d',
                ];
        }
    }

    public function messages()
    {
        return [
            'title.required' => 'You must provide a title.',
            'content.required' => 'You must provide content.',
            'action_text.required' => 'Action Text must be provided.',
            'app_version.required' => 'You must specify an app version.',
            'app_version.regex' => 'App version must follow the Semver pattern - X.X.X.',
            'platform.required' => 'You must specify a platform, either android or iOS.',
            'not_before.required' => 'Date must be formatted Y-m-d.',
            'not_after.required' => 'Date must be formatted Y-m-d.',
        ];
    }
}
