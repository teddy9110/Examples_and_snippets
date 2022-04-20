<?php

namespace Database\Factories;

use Rhf\Modules\Product\Models\PromotedProduct;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExerciseCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'exercise_level_id' => array_rand(array_flip([1, 2])),
            'exercise_location_id' => array_rand(array_flip([1, 2])),
            'facebook_id' => mt_rand(1000000, 9999999),
            'content_video' => '',
            'exercise_frequency_id' => array_rand(array_flip([1, 2, 3])),
            'order' => null,
            'thumbnail' => '',
            'content_thumbnail' => '',
            'promoted_product_id' => PromotedProduct::factory()->create()->id,
            'descriptive_title' => $this->faker->sentence(10)
        ];
    }
}
