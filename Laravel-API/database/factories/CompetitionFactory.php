<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Rhf\Modules\Competition\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $rulesArray = [
            [
                'title' => $this->faker->sentence(3),
                'description' => $this->faker->sentence(5),
                'icon' => 'clock_icon'
            ],
            [
                'title' => $this->faker->sentence(3),
                'description' => $this->faker->sentence(5),
                'icon' => 'heart_icon'
            ]
        ];

        $descriptionArray = [
            [
                'title' => $this->faker->sentence(2),
                'description' => $this->faker->sentence(5)
            ],
            [
                'title' => $this->faker->sentence(2),
                'description' => $this->faker->sentence(10)
            ],
        ];

        return [
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->sentence(3),
            'description' => json_encode($descriptionArray, true),
            'desktop_image' => '/competition/1/' . Str::random(12) . 'png',
            'mobile_image' => '/competition/1/' . Str::random(12) . 'png',
            'app_image' => '/competition/1/' . Str::random(12) . 'png',
            'rules' => json_encode($rulesArray, true),
            'prize' => $this->faker->sentence(3),
            'start_date' => Carbon::now()->subWeek(1)->format('Y-m-d'),
            'end_date' => Carbon::now()->addWeeks(2)->format('Y-m-d'),
            'active' => true
        ];
    }
}
