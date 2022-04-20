<?php

namespace Rhf\Modules\Shopify\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $shopifyUrl = 'gid://shopify/' . ucfirst(current($this->shopify_type)->text) . '/' . $this->shopify_id[0]->text;
        $title = explode('/', $this->banner_link[0]->text);
        return [
            'title' => end($title),
            'mobile_image' => $this->banner_image->mobile->url,
            'type' => current($this->shopify_type)->text,
            'product_url' => base64_encode($shopifyUrl)
        ];
    }
}
