<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class RecipeRequest extends Request
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
            'active' => 'required|boolean',
            'title' => 'required|string|max:255',
            'serves' => 'required|string|max:255',
            'prep_time' => 'nullable|string|max:255',
            'total_time' => 'required|string|max:255',
            'image' => [
                ($this->method() === 'POST' ? 'required' : 'nullable'),
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],

            'macros.calories' => 'required|numeric|min:0',
            'macros.protein' => 'required|numeric|min:0',
            'macros.carbs' => 'required|numeric|min:0',
            'macros.fats' => 'required|numeric|min:0',
            'macros.fibre' => 'required|numeric|min:0',

            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:255',
            'ingredients.*.notes' => 'nullable|string',

            'instructions.*.type' => 'required|in:step,fact',
            'instructions.*.value' => 'required|string'
        ];
    }
}
