<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProgressResource extends JsonResource
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
            'date' => !is_null($this->date) ? $this->date : $this->created_at->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'weight_value' => $this->weight_value,
            'progressPictures' => UserProgressPictureResource::collection($this->progressPicture)
        ];
    }
}
