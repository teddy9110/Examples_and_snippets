<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminNotificationRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:50',
            'content' => 'required|string|max:255',
            'image' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000',
            ],
            'link' => 'nullable', //needs regex
            'data' => 'nullable|array',
            'send_at' => 'nullable',
            'send_now' => 'boolean',
            'subtopic_id' => 'required|exists:sub_topics,id',
        ];
    }
}
