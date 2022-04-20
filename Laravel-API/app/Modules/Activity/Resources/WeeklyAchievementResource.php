<?php

namespace Rhf\Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyAchievementResource extends JsonResource
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
            'medal' => $this->getWeeklyMedal(),
        ];

        return $return;
    }
}
