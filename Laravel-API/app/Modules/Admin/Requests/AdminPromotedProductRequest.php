<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Modules\Product\Enums\PromotedProductType;

class AdminPromotedProductRequest extends Request
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
            'placement_slug' => 'sometimes|nullable|exists:promoted_product_placements,slug',
            'type' => [
                'required',
                Rule::in(PromotedProductType::getValues()),
            ],
            'value' => 'required|string|max:255',
            'active' => 'sometimes|boolean',
            'name' => 'required|string|max:255',
            'image' => [
                $this->method() === 'POST' ? 'required' : 'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'product_content' => 'sometimes|string|max:255',
            'video_url' => 'sometimes|string',
        ];
    }
}
