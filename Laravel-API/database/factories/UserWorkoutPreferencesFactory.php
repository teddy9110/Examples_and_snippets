<?php

namespace Database\Factories;

use Rhf\Modules\Workout\Models\WorkoutPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserWorkoutPreferencesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkoutPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'schedule' => null,
            'exercise_location_id' => array_rand(array_flip([1, 2])),
            'exercise_frequency_id' => array_rand(array_flip([1, 2, 3])),
            'exercise_level_id' => array_rand(array_flip([1, 2])),
        ];
    }
}
