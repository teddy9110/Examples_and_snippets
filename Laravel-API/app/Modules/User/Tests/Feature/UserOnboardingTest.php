<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Rhf\Modules\User\Models\UserPreferences;
use Tests\Feature\ApiTest;

class UserOnboardingTest extends ApiTest
{
    use DatabaseTransactions;

    public $userPreferences;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setUserActive();
        $this->setupExercise();
        $this->setupUserPreferences();

        $this->userPreferences = UserPreferences::factory()->make([
           'user_id' => $this->user->id,
           'user_role' => $this->user->role_id,
            'start_height' => rand('140', '210'),
            'start_weight' => rand('130', '420'),
            'gender' => $this->user->preferences->gender,
            'exercise_location_id' => $this->location,
            'exercise_frequency_id' => $this->freq,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    //** @test */
    public function testUserCanOnboard()
    {
        $setTargets = $this->postUserResponse(
            'PATCH',
            '/account/details',
            $this->userPreferences->toArray()
        );

        $response = $this->getUserResponse(
            'GET',
            '/account/targets'
        );
        $response->assertStatus(200);
    }
}
