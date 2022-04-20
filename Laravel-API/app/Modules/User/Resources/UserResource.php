<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;

class UserResource extends JsonResource
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
        $return = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'surname' => $this->surname,
            'email' => $this->email,
            'payment_status' => $this->paid == 1,
            'subscribed' => $this->paid > 0 && $this->expiry_date->gt(now()->hour(0)->minute(0)->second(1)),
            'subscription' => $this->subscription_data,
            'staff_user' => $this->staff_user,
        ];

        foreach ($this->preferences->getFillable() as $item) {
            if ($item == 'user_role') {
                continue;
            }

            if (
                $item == 'marketing_email_consent' ||
                $item == 'medical_conditions_consent' ||
                $item == 'tutorial_complete'
            ) {
                $return[$item] = $this->preferences[$item] == 1;
                continue;
            }

            if (method_exists($this, 'calculate_' . $item)) {
                $method = 'calculate_' . $item;
                $meta = $this->$method();
                $return[$meta['key']] = $meta['value'];
            } else {
                $transformed = $this->preferences[$item];
                if ($item != 'user_id' && $item != 'period_tracker') {
                    $transformed = isset($transformed) ? strval($transformed) : null;
                }

                $return[$item] = $transformed;
            }
        }

        return $return;
    }

    /**
     * Retrieve exercise frequency value.
     *
     * @return array
     */
    // phpcs:ignore
    private function calculate_exercise_frequency_id()
    {
        $meta = ExerciseFrequency::where('id', '=', $this->getPreference('exercise_frequency_id'))->first();
        $prop = api_version() ? 'slug' : 'amount';
        return [
            'key' => 'exercise_frequency',
            'value' => $meta ? (api_version() ? ((string) $meta->{$prop}) : $meta->{$prop}) : null,
        ];
    }

    /**
     * Retrieve exercise location value.
     *
     * @return array
     */
    // phpcs:ignore
    private function calculate_exercise_location_id()
    {
        $meta = ExerciseLocation::where('id', '=', $this->getPreference('exercise_location_id'))->first();
        $prop = api_version() ? 'slug' : 'title';
        return [
            'key' => 'exercise_location',
            'value' => $meta ? $meta->{$prop} : null,
        ];
    }

    /**
     * Retrieve exercise level value,
     *
     *
     * @return array
     */
    // phpcs:ignore
    private function calculate_exercise_level_id()
    {
        $meta = ExerciseLevel::where('id', '=', $this->getPreference('exercise_level_id'))->first();
        return [
            'key'   => 'exercise_level',
            'value' => $meta ? $meta->slug : null,
        ];
    }
}
