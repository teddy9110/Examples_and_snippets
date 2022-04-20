<?php

namespace Rhf\Modules\Activity\Tests\Feature\MedalTest;

use Carbon\Carbon;
use Tests\Feature\ApiTest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BronzeMedalWorkoutsTest extends ApiTest
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setupExercise();
        $this->setupUserPreferences();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserCanGetBronzeMedalWhenWorkingOut()
    {
        $this->setUserActive();
        $this->user->workoutPreferences->exercise_frequency_id = 2;

        $date = Carbon::now();
        $stars = 3;
        $this->createRangeOfActivities($stars, $date);

        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $date->format('Y-m-d') . '/day'
        );
        $response->assertOk();

        $response->assertExactJson(
            [
                'status' => 'success',
                'data' => [
                    'date' => $date->format('Y-m-d'),
                    'medal' => 'Bronze',
                    'stars' => $stars
                ]
            ]
        );
    }
}
