<?php

namespace Rhf\Modules\Content\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasicCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'children' => BasicCategoryResource::collection($this->allChildren),
        ];

        return $return;
    }
}
