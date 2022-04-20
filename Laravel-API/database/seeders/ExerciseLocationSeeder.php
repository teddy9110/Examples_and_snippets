<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Exercise\Models\ExerciseLocation;

class ExerciseLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $home = ExerciseLocation::create([
            'title' => 'Home',
            'slug' => 'home'
        ]);

        $gym = ExerciseLocation::create([
            'title' => 'Gym',
            'slug' => 'gym'
        ]);
    }
}
