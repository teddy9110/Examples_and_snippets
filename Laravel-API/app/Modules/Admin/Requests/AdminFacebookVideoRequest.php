<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class AdminFacebookVideoRequest extends Request
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:4096',
            'url' => 'required|regex:/^(?:https?:\/\/(?:www.)?)?facebook.com\/(?:[.\w\-]+)\/videos\/[\w\-\/]+\/?$/i',
            'thumbnail' => [
                $this->method() === 'POST' ? 'required' : 'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'live' => $this->method() === 'POST' ? 'string' : 'boolean'
        ];
    }
}
