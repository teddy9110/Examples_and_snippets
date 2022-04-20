<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseLevel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $type = array_rand(array_flip(['Athletic', 'Standard']));
        return [
            'title' => $type,
            'slug' => Str::slug($type)
        ];
    }
}
