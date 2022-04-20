<?php

namespace Rhf\Modules\Activity\Tests\Feature\MedalTest;

use Carbon\Carbon;
use Tests\Feature\ApiTest;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SilverMedalWorkoutsTest extends ApiTest
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
    public function testUserCanGetSilverMedalWhenWorkingOut()
    {
        $this->setUserActive();
        $this->user->workoutPreferences->exercise_frequency_id = 2;

        $date = Carbon::now();
        $stars = 5;
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
                    'stars' => $stars,
                    'medal' => 'Silver',
                ]
            ]
        );
    }
}
