<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Exercise\Models\ExerciseLevel;

class ExerciseLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $athletic = ExerciseLevel::create([
            'title' => 'Athletic',
            'slug' => 'athletic',
        ]);

        $standard = ExerciseLevel::create([
            'title' => 'Standard',
            'slug' => 'standard',
        ]);
    }
}
