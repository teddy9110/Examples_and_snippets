<?php

use Illuminate\Database\Seeder;
use Rhf\Modules\User\Models\AppReviewTopic;

class AppReviewTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppReviewTopic::create([
            'title' => 'Dashboard',
        ]);
        AppReviewTopic::create([
            'title' => 'RHTV/Video Content',
        ]);
        AppReviewTopic::create([
            'title' => 'Help Center',
        ]);
        AppReviewTopic::create([
            'title' => 'Chat to a coach',
        ]);
        AppReviewTopic::create([
            'title' => 'Recipes',
        ]);
        AppReviewTopic::create([
            'title' => 'Workouts',
        ]);
        AppReviewTopic::create([
            'title' => 'Shop',
        ]);
    }
}
