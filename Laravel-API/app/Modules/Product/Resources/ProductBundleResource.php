<?php

namespace Rhf\Modules\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductBundleResource extends JsonResource
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
            'title' => $this->title,
            'bundle_slug' => $this->bundle_slug,
            'introduction' => $this->introduction_text,
            'closing' => $this->closing_text,
            'bundle' => $this->bundle
        ];
    }
}
