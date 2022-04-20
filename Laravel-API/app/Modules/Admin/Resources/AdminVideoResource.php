<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Tags\Resources\TagsResource;

class AdminVideoResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'thumbnail' => $this->getVideoThumbnail($this->thumbnail),
            'open_count' => $this->open_count,
            'view_count' => $this->view_count,
            'live' => $this->live,
            'tags' => TagsResource::collection($this->tags),
            'scheduled_date' => $this->scheduled_date,
            'scheduled_time' => $this->scheduled_time,
            'active' => $this->active,
            'order' => $this->order,
        ];
    }
}
