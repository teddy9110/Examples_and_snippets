<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $type = array_rand(array_flip(['Gym', 'Home']));
        return [
            'title' => $type,
            'slug' => Str::slug($type)
        ];
    }
}
