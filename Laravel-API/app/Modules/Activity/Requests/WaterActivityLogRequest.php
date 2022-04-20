<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Rhf\Modules\Activity\Models\Activity;

class WaterActivityLogRequest extends Request
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
                $calculationTypes = implode(
                    ',',
                    [
                        Activity::CALCULATION_TYPE_APPEND,
                        Activity::CALCULATION_TYPE_REPLACE,
                        Activity::CALCULATION_TYPE_SUM,
                    ]
                );

                return [
                    'glasses_of_water' => 'required|int',
                    'calculation_type' => 'sometimes|required|in:' . $calculationTypes,
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

    public function getCalculationType()
    {
        return $this->has('calculation_type') ? $this->get('calculation_type') : null;
    }
}
