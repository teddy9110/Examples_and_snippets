<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFrequencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseFrequency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $amount = array_rand(array_flip([0, 3, 5, 6]));
        return [
            'amount' => $amount,
            'slug' => $amount,
        ];
    }
}
