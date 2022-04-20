<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Rhf\Modules\User\Models\UserPreferences;
use Tests\Feature\ApiTest;

class UserTargetsSetTest extends ApiTest
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
            'progress_picture_consent' => array_rand(array_flip(['unknown', 'accepted', 'rejected'])),
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    //** @test */
    public function testUserHasTargetsSet()
    {
        $this->postUserResponse(
            'PATCH',
            '/account/details',
            $this->userPreferences->toArray()
        );

        $response = $this->getUserResponse(
            'GET',
            '/account/targets'
        );

        $this->assertStringContainsString('success', $response->getContent());
        $response->assertJsonStructure(
            [
                'status',
                'data' => [
                    "daily_calorie_goal",
                    "daily_water_goal",
                    "daily_protein_goal",
                    "daily_step_goal",
                    "daily_fat_goal",
                    "daily_fiber_goal",
                    "daily_carbohydrate_goal",
                ]
            ]
        );
    }
}
