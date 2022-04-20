<?php

namespace Rhf\Modules\Recipe\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipePreviewResource extends JsonResource
{
    protected $meta = [];

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
            'active' => !!$this->active,
            'title' => $this->title,
            'image_uri' => $this->getImage(),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d')
        ];
    }
}
