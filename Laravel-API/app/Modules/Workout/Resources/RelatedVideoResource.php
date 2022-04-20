<?php

namespace Rhf\Modules\Workout\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $this->getImage($this->thumbnail),
        ];
    }
}
