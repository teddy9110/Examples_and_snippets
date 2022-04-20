<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Models\ExerciseLevel;
use Rhf\Modules\Workout\Models\ExerciseLocation;

class WorkoutPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Exercise frequency
         */
        $frequencies = [
            [
                'amount' => 0,
                'slug' => ExerciseFrequency::SLUG_0,
            ],
            [
                'amount' => 3,
                'slug' => ExerciseFrequency::SLUG_3,
            ],
            [
                'amount' => 5,
                'slug' => ExerciseFrequency::SLUG_5,
            ],
            [
                'amount' => 0,
                'slug' => ExerciseFrequency::SLUG_6,
            ],
        ];

        foreach ($frequencies as $item) {
            if (!ExerciseFrequency::where('slug', $item['slug'])->exists()) {
                $i = new ExerciseFrequency();
                $i->amount = $item['amount'];
                $i->slug = $item['slug'];
                $i->save();
            }
        }

        /**
         * Exercise level
         */
        $levels = [
            [
                'title' => 'Athletic',
                'slug' => ExerciseLevel::SLUG_ATHLETIC,
            ],
            [
                'title' => 'Standard',
                'slug' => ExerciseLevel::SLUG_STANDARD,
            ],
        ];

        foreach ($levels as $item) {
            if (!ExerciseLevel::where('slug', $item['slug'])->exists()) {
                $i = new ExerciseLevel();
                $i->title = $item['title'];
                $i->slug = $item['slug'];
                $i->save();
            }
        }

        /**
         * Exercise location
         */
        $locations = [
            [
                'title' => 'Gym',
                'slug' => ExerciseLocation::SLUG_GYM,
            ],
            [
                'title' => 'Home',
                'slug' => ExerciseLocation::SLUG_HOME,
            ],
        ];

        foreach ($locations as $item) {
            if (!ExerciseLocation::where('slug', $item['slug'])->exists()) {
                $i = new ExerciseLevel();
                $i->title = $item['title'];
                $i->slug = $item['slug'];
                $i->save();
            }
        }
    }
}
