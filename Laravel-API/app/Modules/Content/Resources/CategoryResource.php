<?php

namespace Rhf\Modules\Content\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Content\Resources\ContentResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $return = [
            'id' => $this->id,
            'title' => $this->title,
            'parent' => new BasicCategoryResource($this->parent()->first()),
            'children' => BasicCategoryResource::collection($this->allChildren),
            'content' => ContentResource::collection($this->allChildContent() ? $this->allChildContent() : collect()),
        ];

        return $return;
    }
}
