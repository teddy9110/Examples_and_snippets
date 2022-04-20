<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Workout\Models\ExerciseFrequency;

class UserTargetsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $exerciseFrequency = ExerciseFrequency::findOrFail($this->getPreference('exercise_frequency_id'))->slug;
        $return = [
            'daily_calorie_goal' =>
                strval($this->getPreference('daily_calorie_goal') ? $this->getPreference('daily_calorie_goal') : 0),
            'daily_water_goal' =>
                strval($this->getPreference('daily_water_goal') ? $this->getPreference('daily_water_goal') : 0),
            'daily_protein_goal' =>
                strval($this->getPreference('daily_protein_goal') ? $this->getPreference('daily_protein_goal') : 0),
            'daily_step_goal' =>
                strval($this->getPreference('daily_step_goal') ? $this->getPreference('daily_step_goal') : 0),
            'daily_fat_goal' =>
                strval($this->getPreference('daily_fat_goal') ? $this->getPreference('daily_fat_goal') : 0),
            'daily_fiber_goal' =>
                strval($this->getPreference('daily_fiber_goal') ? $this->getPreference('daily_fiber_goal') : 0),
            'daily_carbohydrate_goal' => strval(
                $this->getPreference('daily_carbohydrate_goal') ? $this->getPreference('daily_carbohydrate_goal') : 0
            ),
            'exercise_frequency' => $exerciseFrequency
        ];

        return $return;
    }
}
