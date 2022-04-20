<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class AdminSyncRelatedVideosRequest extends Request
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
            'related_videos.*.id' => 'sometimes',
            'related_videos.*.title' => 'required|string|max:255',
            'related_videos.*.url' => [
                'required',
                'regex:/^(?:https?:\/\/(?:www.)?)?youtube.com\/watch\?v=/i',
            ],
            'related_videos.*.thumbnail' => [
                'required_without:related_videos.*.id',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
            ],
            'related_videos.*.order' => 'required|integer'
        ];
    }
}
