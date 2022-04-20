<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminExerciseRequest extends FormRequest
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

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'descriptive_title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'thumbnail' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'video' => 'nullable',

            'quantity' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ];
    }
}
