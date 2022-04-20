<?php

namespace Database\Factories\Activity;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\Activity\Models\Activity;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => Auth::id(),
            'date' => date('Y-m-d'),
        ];
    }

    /**
     * Indicate part of the model that should be modified
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function modifier(String $type, Int $lower, Int $higher)
    {
        return $this->state(function (array $attributes) use ($type, $lower, $higher) {
            return [
                'type' => $type,
                'value' => mt_rand($lower, $higher),
            ];
        });
    }
}
