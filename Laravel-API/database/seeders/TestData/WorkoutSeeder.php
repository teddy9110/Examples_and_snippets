<?php

namespace Database\Seeders\TestData;

use Illuminate\Database\Seeder;
use Rhf\Modules\Exercise\Models\Exercise;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseCategory;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = ExerciseLevel::get();
        $categories = ExerciseCategory::get();

        // Create the exercises for a workout levels and categories
        foreach ($levels as $level) {
            foreach ($categories as $category) {
                $exercise = Exercise::create([
                    'exercise_level_id' => $level->id,
                    'title' => $level->title.' '.$category->title.' Exercise',
                    'content' => 'An exercise for your '.$category->title.' if you are at '.$level->title.' level.',
                ]);
                $exercise->categories()->attach($category);
            }
        }
    }
}
