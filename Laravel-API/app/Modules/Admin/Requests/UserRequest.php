<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class UserRequest extends Request
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
        if (isset($this->route()->parameters()['id'])) {
            $id = $this->route()->parameters()['id'];
        } else {
            $id = 0;
        }

        switch ($this->method()) {
            case 'POST':
                return [
                    // General rules
                    'first_name'    => 'max:255',
                    'surname'       => 'max:255',
                    'email'         => 'required|email|unique:users,email,' . $id,
                    'paid'          => 'in:0,1',
                    'expiry_date'   => 'nullable|date',
                    'user_role'     => 'required|exists:user_roles,id',

                    // Meta
                    'meta.dob'                   => 'nullable|date',
                    'meta.gender'                => 'in:Male,Female',
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
