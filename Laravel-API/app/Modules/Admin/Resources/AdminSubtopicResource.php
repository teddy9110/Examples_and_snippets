<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Notifications\Models\Topics;

class AdminSubtopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'active' => $this->active,
            'subscribe' => $this->subscribe,
            'parent' => $this->topic->category,
            'topic_id' => $this->topic_id
        ];
    }
}
