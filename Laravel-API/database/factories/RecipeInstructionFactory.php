<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rhf\Modules\Recipe\Models\RecipeInstruction;

class RecipeInstructionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RecipeInstruction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['step','fact']),
            'value' => $this->faker->sentence()
        ];
    }
}
