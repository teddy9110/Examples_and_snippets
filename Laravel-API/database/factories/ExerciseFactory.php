<?php

namespace Database\Factories;

use Rhf\Modules\Exercise\Models\Exercise;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exercise::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $video = $this->faker->randomNumber(8);
        return [
            'exercise_category_id' => ExerciseCategory::factory()->create()->id,
            'sort_order' => null,
            'title' => $this->faker->word(2),
            'quantity' => '4 Sets 8-12 Reps',
            'content' => $this->faker->sentence(10),
            'video' => $video,
            'content_video' => 'workout-videos/exercise/' . $video . '.mp4',
            'thumbnail' => '',
            'content_thumbnail' => '',
            'descriptive_title' => $this->faker->sentence(10)
        ];
    }
}
