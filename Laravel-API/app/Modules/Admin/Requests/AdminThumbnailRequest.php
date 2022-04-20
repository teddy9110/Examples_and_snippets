<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest as Request;

class AdminThumbnailRequest extends Request
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
            'thumbnail_type' => [
                'required',
                Rule::in(['youtube_flow_thumbnail', 'standard_flow_thumbnail', 'content_thumbnail']),
            ],
            'thumbnail' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ];
    }
}
