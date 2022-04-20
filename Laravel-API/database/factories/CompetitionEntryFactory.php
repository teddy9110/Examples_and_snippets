<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Rhf\Modules\Competition\Models\CompetitionEntry;

class CompetitionEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompetitionEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'description' => $this->faker->paragraph(2, 6),
            'image' => 'competition/1/entries/' . Str::random(24) . '.jpg'
        ];
    }
}
