<?php

namespace Rhf\Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityNoteResource extends JsonResource
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
            'note' => $this->note,
            'period' => $this->period,
            'date' => $this->date,
            'body_fat_percentage' => $this->body_fat_percentage
        ];
    }
}
