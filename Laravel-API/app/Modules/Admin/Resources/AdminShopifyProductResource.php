<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminShopifyProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'website_image' => $this->getImage($this->website_image),
            'mobile_image' => $this->getImage($this->mobile_image),
            'active' => $this->active,
            'website_only' => $this->website_only,
            'shopify_id' => $this->shopify_product_id,
            'shopify_type' => $this->shopify_product_type
        ];
    }
}
