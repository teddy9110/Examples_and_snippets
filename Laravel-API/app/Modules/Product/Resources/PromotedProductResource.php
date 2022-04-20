<?php

namespace Rhf\Modules\Product\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $articleId = config('app.product_article_id');
        if ($this->id === config('app.gym_workout_bundle_id')) {
            $articleId = config('app.gym_article_id');
        }

        $type = $this->type;
        $result = [
            'id' => $this->id,
            'placement_slug' => $this->placement_slug,
            'type' => $this->type,
            'name' => $this->name,
            'image_uri' => $this->getImage(),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'video_url' => $this->video_url,
            'product_article_id' => $articleId
        ];

        if ($type == 'shopify-category') {
            $result['category_id'] = $this->value;
        } elseif ($type == 'shopify-product') {
            $result['product_id'] = $this->value;
        }

        return $result;
    }
}
