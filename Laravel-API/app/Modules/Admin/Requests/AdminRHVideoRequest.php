<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;

class AdminRHVideoRequest extends Request
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
                    'title' => 'required|string|max:100',
                    'description' => 'required|string',
                    'url' => 'required|active_url',
                    'thumbnail' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
                    ],
                    'tags' => [
                        'sometimes',
                        'array',
                        Rule::exists('tags', 'id')->where('type', 'video')
                    ],
                    'scheduled_date' => 'sometimes|nullable|date_format:Y-m-d',
                    'scheduled_time' => 'sometimes|nullable|date_format:H:i',
                    'active' => 'sometimes|boolean',
                ];
            case 'PUT':
                return [
                    'title' => 'required|string|max:100',
                    'description' => 'required|string',
                    'url' => 'required|active_url',
                    'tags' => [
                        'sometimes',
                        'array',
                        Rule::exists('tags', 'id')->where('type', 'video')
                    ],
                    'scheduled_date' => 'sometimes|nullable|date_format:Y-m-d',
                    'scheduled_time' => 'sometimes|nullable|date_format:H:i',
                    'active' => 'sometimes|boolean',
                ];
            default:
                return [];
        }
    }
}
