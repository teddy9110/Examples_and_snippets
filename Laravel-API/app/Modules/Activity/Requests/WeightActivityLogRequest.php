<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class WeightActivityLogRequest extends Request
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
                    'weight' => 'required|numeric|min:1',
                    'details' => 'array',
                    'details.note' => 'nullable|string|max:255',
                    'details.period' => 'in:true,false,unknown'
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
