<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class ManagePendingUserRequest extends Request
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
            case 'PATCH':
            case 'GET':
            case 'DELETE':
            case 'PUT':
                return [];
            case 'POST':
                return [
                    'first_name'            => 'string|required_if:paid,1',
                    'surname'               => 'string|required_if:paid,1',
                    'email'                 => 'string|required',
                    'paid'                  => 'required|in:0,1',
                    'expires'               => 'required',
                    // TODO: Create a new request to use in DirectDebitController
                    'next_payment_date'     => 'sometimes',
                ];
            default:
                break;
        }
    }
}
