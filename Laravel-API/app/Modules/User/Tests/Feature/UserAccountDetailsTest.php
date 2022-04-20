<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use Tests\Feature\ApiTest;

class UserAccountDetailsTest extends ApiTest
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
            // 'exercise_level_id' => $this->level,
            'exercise_location_id' => $this->location,
            'exercise_frequency_id' => $this->freq,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserCanRetrieveAccountDetails()
    {
        $response = $this->getUserResponse('GET', '/account/details');

        $this->assertAuthenticated('api');
        $this->assertStringContainsString('success', $response->getContent());
        $response->assertStatus(200);
        $response->assertJson(
            [
                'status' => 'success',
                'data' => [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                ]
            ]
        ); // test that JSON contains
        $response->assertJsonStructure(
            [
                'status',
                'remaining_updates',
                'data' => [
                    'id',
                    'first_name',
                    'surname',
                    'email',
                    'payment_status',
                    'subscribed',
                    "user_id",
                    "weight_unit",
                    "gender",
                    "dob",
                    "daily_step_goal",
                    "start_height",
                    "start_weight",
                    "exercise_location",
                    "exercise_frequency",
                    "daily_water_goal",
                    "daily_calorie_goal",
                    "exercise_level",
                    "daily_protein_goal",
                    "daily_carbohydrate_goal",
                    "daily_fat_goal",
                    "daily_fiber_goal",
                    "personal_goals",
                    "medical_conditions",
                    "marketing_email_consent",
                    "medical_conditions_consent",
                    "tutorial_complete",
                    "progress_picture_consent",
                ],
            ]
        ); // test JSON structure
    }
}
