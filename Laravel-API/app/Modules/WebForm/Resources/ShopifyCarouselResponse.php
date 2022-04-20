<?php

namespace Rhf\Modules\WebForm\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopifyCarouselResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'website-image' => $this->website_image,
            'mobile-image' => $this->mobile_image,
            'website_only' => $this->website_only,
            'shopify_id' => $this->shopify_product_id,
            'shopify_type' => $this->shopify_product_type
        ];
    }
}
