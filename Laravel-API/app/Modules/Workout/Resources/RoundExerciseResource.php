<?php

namespace Rhf\Modules\Workout\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoundExerciseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order' => $this->order,
            'repeat' => $this->repeat,
            'quantity' => $this->quantity,
            'exercise' => new ExerciseResource($this->whenLoaded('exercise')),
        ];
    }
}
