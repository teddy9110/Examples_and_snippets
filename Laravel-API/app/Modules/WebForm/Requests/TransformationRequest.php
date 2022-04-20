<?php

namespace Rhf\Modules\WebForm\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransformationRequest extends FormRequest
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
        $imageRules = [
            'required',
            'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
            'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
        ];

        return [
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'date_of_birth' => 'required|string|date_format:Y-m-d',
            'gender' => 'required|string',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'weight_loss' => 'required|string',
            'start_weight' => 'required|string',
            'current_weight' => 'required|string',
            'story' => 'required|string|max:255|min:10',
            'before_photo' => $imageRules,
            'after_photo' => $imageRules,
            'marketing_accepted' => 'required|string|in:true,false',
            'remain_anonymous' => 'required|string|in:true,false',
        ];
    }
}
