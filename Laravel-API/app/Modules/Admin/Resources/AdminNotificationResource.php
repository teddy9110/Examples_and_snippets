<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminNotificationResource extends JsonResource
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
            'content' => $this->content,
            'topic' => [
                'id' => $this->topic->id,
                'category' => $this->topic->category,
                'sub-category' => $this->topic->sub_category,
                'slug' => $this->topic->slug,
            ],
            'data' => $this->data,
            'image' => $this->image,
            'link' => $this->link,
            'send_at' => $this->send_at,
        ];
    }
}
