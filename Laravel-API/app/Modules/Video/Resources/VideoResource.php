<?php

namespace Rhf\Modules\Video\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Tags\Resources\TagsResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $date = $this->created_at;
        if ($this->active == 1 && !is_null($this->scheduled_date)) {
            $date = iso_date($this->scheduled_date);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'thumbnail' => $this->getVideoThumbnail($this->thumbnail),
            'open_count' => $this->open_count,
            'view_count' => $this->view_count,
            'date' => $date,
            'live' => $this->live === 1 ? true : false,
            'tags' => !$this->tags->isEmpty() ? TagsResource::collection($this->whenLoaded('tags')) : null,
        ];
    }
}
