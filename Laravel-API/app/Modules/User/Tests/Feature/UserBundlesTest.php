<?php

namespace Rhf\Modules\User\Tests\Feature;

use Database\Seeders\ProductBundleSeeder;
use Tests\Feature\ApiTest;
use Rhf\Modules\User\Models\UserPreferences;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserBundlesTest extends ApiTest
{
    use DatabaseTransactions;

    public $level;
    public $location;
    public $freq;
    public $userPreferences;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setUserActive();
        $this->setupUserPreferences();
        $this->setupExercise();

        $seeder = (new ProductBundleSeeder())->run();

        $this->userPreferences = UserPreferences::factory()->make(
            [
                'start_height' => rand('140', '210'),
                'start_weight' => rand('130', '420'),
                'gender' => $this->user->preferences->gender,
                'user_role' => $this->user->role_id,
                'exercise_level_id' => $this->level,
                'exercise_location_id' => $this->location,
                'exercise_frequency_id' => $this->freq,
            ]
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserBundlesAreReturned()
    {
        $response = $this->getUserResponse('GET', '/account/bundles');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ])
            ->assertJsonStructure([
                'status',
                'data'
            ]);
    }
}
