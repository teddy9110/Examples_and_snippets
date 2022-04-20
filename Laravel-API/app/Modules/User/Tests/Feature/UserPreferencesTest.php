<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use Tests\Feature\ApiTest;

class UserPreferencesTest extends ApiTest
{
    use DatabaseTransactions;

    public $userPreferences;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setUserActive();
        $this->setupUserPreferences();
        $this->setupExercise();

        $this->userPreferences = UserPreferences::factory()->make([
            'start_height' => rand('140', '210'),
            'start_weight' => rand('130', '420'),
            'gender' => $this->user->preferences->gender,
            'user_role' => $this->user->role_id,
        ]);

        $this->workoutPreferences = WorkoutPreference::factory()->make([
            'schedule' => null,
            'exercise_level_id' => $this->level,
            'exercise_location_id' => $this->location,
            'exercise_frequency_id' => $this->freq,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserPreferencesCanBePatched()
    {
        $response = $this->postUserResponse(
            'PATCH',
            '/account/details',
            [
                'start_height' => rand('140', '210'),
                "start_weight" => rand('130', '420'),
                "daily_step_goal" => array_rand(array_flip(['5000', '10000', '15000', '20000', '25000'])),
                "dob" => date('Y-m-d', (mt_rand(1, 1104497999))),
                "gender" => $this->user->preferences->gender,
                'user_role' => $this->user->role_id,
            ]
        );

        $response->assertOk();
        $this->assertStringContainsString('success', $response->getContent());
    }
}
