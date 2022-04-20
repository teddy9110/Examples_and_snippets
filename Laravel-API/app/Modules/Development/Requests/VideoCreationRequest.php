<?php

namespace Rhf\Modules\Development\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;

class VideoCreationRequest extends Request
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
        return [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'url' => 'required|url|active_url',
            'tags' => [
                'sometimes',
                'array',
                Rule::exists('tags', 'id')->where('type', 'video')
            ],
            'scheduled_date' => 'sometimes|nullable|date_format:Y-m-d|after_or_equal:today',
            'scheduled_time' => 'sometimes|nullable|date_format:H:i',
            'active' => 'sometimes|boolean',
        ];
    }
}
