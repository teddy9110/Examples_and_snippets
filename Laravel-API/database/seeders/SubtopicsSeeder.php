<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Notifications\Models\SubTopics;

class SubtopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubTopics::create([
            'title' => 'Updates',
            'slug' => 'updates',
            'description' => 'Service accounts updates',
            'topic_id' => 4
        ]);

        SubTopics::create([
            'title' => 'Protein',
            'slug' => 'protein',
            'description' => 'Be informed about our new protein flavours',
            'topic_id' => 2
        ]);

        SubTopics::create([
            'title' => 'Video',
            'slug' => 'video',
            'description' => 'Enable notifications to find out when we are live',
            'topic_id' => 3
        ]);
    }
}
