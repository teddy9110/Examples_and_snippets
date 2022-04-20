<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class AdminCompetitionRequest extends Request
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

     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'title' => 'required|string',
                    'subtitle' => 'required|string',
                    'description' => 'required|array',
                    'description.*.title' => 'required',
                    'description.*.description' => 'required',
                    'desktop_image' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=2048,max_height=2048',
                    ],
                    'mobile_image' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=2048,max_height=2048',
                    ],
                    'app_image' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=2048,max_height=2048',
                    ],
                    'rules' => 'required|array',
                    'prize' => 'required|string',
                    'start_date' => 'required|date_format:Y-m-d',
                    'end_date' => 'required|date_format:Y-m-d',
                    'active' => 'sometimes',
                ];
            case 'PUT':
                return [
                    'title' => 'required|string',
                    'subtitle' => 'required|string',
                    'description' => 'required|array',
                    'description.*.title' => 'required',
                    'description.*.description' => 'required',
                    'rules' => 'required|array',
                    'prize' => 'required|string',
                    'start_date' => 'required|date_format:Y-m-d',
                    'end_date' => 'required|date_format:Y-m-d',
                    'active' => 'sometimes',
                    'desktop_image' => 'sometimes',
                    'mobile_image' => 'sometimes',
                    'app_image' => 'sometimes',
                ];
            default:
                return [];
        }
    }
}
