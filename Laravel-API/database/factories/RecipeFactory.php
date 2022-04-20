<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Recipe\Models\Recipe;

class RecipeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'title'  => $this->faker->title,
            'serves' => $this->faker->numberBetween(1, 8),
            'active' => $this->faker->boolean(50),
            'prep_time' => $this->faker->numberBetween(1, 120),
            'total_time' => $this->faker->numberBetween(1, 120),
            'image' => UploadedFile::fake()->image(Str::random('22') . '.jpg', 1024, 1024)->size(900),
            'macro_calories' => $this->faker->numberBetween(500, 2000),
            'macro_protein' => $this->faker->numberBetween(15, 40),
            'macro_carbs' => $this->faker->numberBetween(40, 100),
            'macro_fats' => $this->faker->numberBetween(20, 55),
            'macro_fibre' => $this->faker->numberBetween(15, 45),
        ];
    }
}
