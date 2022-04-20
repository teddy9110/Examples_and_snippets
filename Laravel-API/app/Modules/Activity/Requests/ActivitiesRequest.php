<?php

namespace Rhf\Modules\Activity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivitiesRequest extends FormRequest
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
            case 'POST':
                return [
                    'type' => 'required|in:steps,water,weight',
                    'value' => $this->getValueRules(),
                    'date' => 'required|date_format:Y-m-d|before:' . date('Y-m-d', strtotime('+2 days')),
                    'details' => 'array',
                    'details.note' => 'nullable|string|max:255',
                    'details.period' => 'in:true,false,unknown'
                ];
            case 'PATCH':
                return [
                    'value' => $this->getValueRules(),
                    'date' => 'required|date_format:Y-m-d|before:' . date('Y-m-d', strtotime('+2 days')),
                    'details' => 'array',
                    'details.note' => 'nullable|string|max:255',
                    'details.period' => 'in:true,false,unknown',
                    'details.body_fat_percentage' => 'sometimes|numeric',
                ];
            case 'GET':
            case 'DELETE':
            case 'PUT':
                return [];
            default:
                break;
        }
    }

    private function getValueRules()
    {
        if (api_version() >= 20210914) {
            return 'required|' . (
                $this->input('type') == 'water' ?
                'integer|min:1|max:1000000' :
                'numeric|min:1|max:1000000'
            );
        } else {
            return 'required|numeric';
        }
    }
}
