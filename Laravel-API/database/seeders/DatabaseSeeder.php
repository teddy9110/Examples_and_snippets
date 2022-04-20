<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // UsersTableSeeder::class,
            // ExerciseSeeder::class,
            // UserRolesSeeder::class,
            // RecipeSeeder::class,
            // ProductBundleSeeder::class,
            // TopicsSeeder::class,
            // WorkoutPreferenceSeeder::class,
        ]);
    }
}
