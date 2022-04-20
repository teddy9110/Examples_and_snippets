<?php

namespace Rhf\Modules\Shopify\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->item->title,
            'body' => $this->item->body_html,
            'image' => $this->item->image->src,
            'product_url' => base64_encode($this->item->admin_graphql_api_id),
            'type' => empty($this->item->product_type) ? 'product' : 'collection'
        ];
    }
}
