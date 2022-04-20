<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminExerciseResource extends JsonResource
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
            'descriptive_title' => $this->descriptive_title,
            'content' => $this->content,
            'thumbnail' => $this->thumbnail,
            'content_thumbnail' => $this->content_thumbnail,
            'video' => $this->video,
            'content_video' => $this->content_video,
            'sort_order' => $this->sort_order,
            'quantity' => $this->quantity,
        ];
    }
}
