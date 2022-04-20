<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminWorkoutRoundExerciseResource extends JsonResource
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
            'exercise_id' => $this->exercise_id,
            'quantity' => $this->quantity,
            'order' => $this->order,
        ];
    }
}
