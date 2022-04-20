<?php

namespace Rhf\Modules\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationPreferencesRequest extends FormRequest
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
            case 'PATCH':
                return [
                    'notifications' => 'required|array',
                ];
            case 'GET':
                return [];
            case 'DELETE':
                return [
                    'device_token' => 'required|string'
                ];
            case 'PUT':
                return [
                    'device_token' => 'required|string'
                ];
            case 'POST':
                return [];
            default:
                break;
        }
    }
}
