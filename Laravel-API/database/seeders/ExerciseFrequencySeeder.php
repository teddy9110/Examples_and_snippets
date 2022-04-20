<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;

class ExerciseFrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $freq0 = ExerciseFrequency::create([
            'amount' => 0,
            'slug' => 0,
        ]);

        $freq3 = ExerciseFrequency::create([
            'amount' => 3,
            'slug' => 3,
        ]);

        $freq6 = ExerciseFrequency::create([
           'amount' => 6,
           'slug' => 6,
        ]);

        $freq5 = ExerciseFrequency::create([
            'amount' => 5,
            'slug' => 5,
         ]);
    }
}
