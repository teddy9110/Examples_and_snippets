<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Notifications\Models\Topics;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Topics::create([
            'category' => 'Reminders',
        ]);

        Topics::create([
            'category' => 'Marketing',
            'slug' => 'reminders'
        ]);

        Topics::create([
            'category' => 'Live Video',
        ]);

        Topics::create([
            'category' => 'Service Updates',
        ]);
    }
}
