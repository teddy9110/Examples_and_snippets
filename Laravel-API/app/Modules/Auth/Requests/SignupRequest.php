<?php

namespace Rhf\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class SignupRequest extends Request
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

    // OPTIONAL OVERRIDE
    public function forbiddenResponse()
    {
    }

    /**
     * Get the validation rules that apply to the request.

     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'email' => 'required|email|exists:users,email',
                    'password'   => 'required|string',
                ];
            case 'GET':
            case 'DELETE':
            case 'PUT':
            case 'PATCH':
                return [];
            default:
                break;
        }
    }
}
