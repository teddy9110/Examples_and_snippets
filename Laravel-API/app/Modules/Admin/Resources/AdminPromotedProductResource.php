<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPromotedProductResource extends JsonResource
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
            'placement_slug' => $this->placement_slug,
            'type' => $this->type,
            'value' => $this->value,
            'name' => $this->name,
            'image_uri' => $this->getImage(),
            'active' => !!$this->active,
            'video_url' => $this->video_url,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
