<?php

namespace Rhf\Modules\Workout\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoundResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'order' => $this->order,
            'repeat ' => $this->repeat   ,
            'thumbnail' => $this->thumbnail,
            'video' => $this->video,
            'exercises' => RoundExerciseResource::collection($this->whenLoaded('roundExercises')),
        ];
    }
}
