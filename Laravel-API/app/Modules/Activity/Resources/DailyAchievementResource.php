<?php

namespace Rhf\Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyAchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $return = [
            'date' => $this->getDate()->format('Y-m-d'),
            'medal' => $this->getMedal(),
            'stars' => $this->getTotalStars(),
        ];

        return $return;
    }
}
