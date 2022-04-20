<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminContentResource extends JsonResource
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
            'category' => new AdminCategoryResource($this->category),
            'type' => $this->type,
            'title' => $this->title,
            'created_at' => $this->created_at->format('Y-m-d'),
            'order' => $this->order
        ];
    }
}
