<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class AdminShopifyPromotedProductRequest extends Request
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
                    'title' => 'required|string|max:255',
                    'website_image' => [
                        'required',
                        'mimes:jpeg,JPEG,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=2300,max_height=750',
                    ],
                    'mobile_image' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=800,max_height=450',
                    ],
                    'active' => 'sometimes|string|in:true,false',
                    'website_only' => 'sometimes|string|in:true,false',
                    'shopify_product_id' => 'required|int',
                    'shopify_product_type' => 'required|string'
                ];
            case 'PUT':
                return [
                    'title' => 'required|string|max:255',
                    'active' => 'sometimes|boolean',
                    'website_only' => 'sometimes|boolean',
                    'shopify_product_id' => 'required|int',
                    'shopify_product_type' => 'required|string'
                ];
            default:
                return [];
        }
    }
}
