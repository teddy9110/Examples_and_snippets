<?php

namespace Rhf\Modules\Development\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\User;
use Tests\Feature\ApiTest;
use Illuminate\Support\Facades\Auth;

class DevelopmentActivitiesTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupUser();
        $this->setupUserPreferences();
        $this->setupExercise();
        $this->unsetUserMFP();
    }

    /** @test */
    public function testThatActivitiesAreCreated()
    {
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data->id;

        $response = $this->postUserResponse(
            'POST',
            '/development/seed-user-activities',
            [
                'start_date' => '2021-05-18',
                'end_date' => '2021-10-01',
                'user_id' => $testUser,
                'types' => ['steps']
            ]
        );
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    'activity_index',
                    'activities_count',
                ]
            ]
        );
    }

    /** @test */
    public function testThatActivitiesAreNotCreated()
    {
        $response = $this->makeUser();
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data->id;

        $response = $this->postUserResponse(
            'POST',
            '/development/seed-user-activities',
            [
                'end_date' => '2021-10-01',
                'user_id' => $testUser,
            ]
        );
        $response->assertStatus(422);
    }

    /** @test */
    public function testThatSilverMedalAcivitiesAreCreated()
    {
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data;
        User::find($testUser->id)->update(['created_at' => '2021-09-10']);
        $testUser = User::find($testUser->id);
        Auth::login($testUser, true);
        $dates = '2021-09-12';
        $response = $this->postUserResponse(
            'POST',
            '/development/set-daily-medal',
            [
                'user_id' => $testUser->id,
                'medals' => [
                    [
                        'type' => 'Silver',
                        'dates' => [$dates]
                    ]
                ],
                'user_id' => $testUser->id
            ]
        );
        $response->assertStatus(204);
        $testUser = User::find($testUser->id);
        Auth::login($testUser, true);
        $response2 = $this->actingAs($testUser)->getUserResponse(
            'GET',
            '/achievement/medal/' . $dates . '/day'
        );
        $response2->assertOk();
    }

    /** @test */
    public function testThatBronzeMedalAcivitiesAreCreated()
    {
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data;
        User::find($testUser->id)->update(['created_at' => '2021-09-10']);
        $testUser = User::find($testUser->id);
        Auth::login($testUser, true);
        $dates = '2021-09-12';
        $response = $this->postUserResponse(
            'POST',
            '/development/set-daily-medal',
            [
                'user_id' => $testUser->id,
                'medals' => [
                    [
                        'type' => 'Bronze',
                        'dates' => [$dates]
                    ]
                ],
                'user_id' => $testUser->id

            ]
        );
        $response->assertStatus(204);
        $response = $this->actingAs($testUser)->getUserResponse(
            'GET',
            '/achievement/medal/' . $dates . '/day'
        );
        $response->assertOk();
    }

    /** @test */
    public function testThatGoldMedalAcivitiesAreCreated()
    {
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data;
        User::find($testUser->id)->update(['created_at' => '2021-09-10']);
        $testUser = User::find($testUser->id);
        Auth::login($testUser, true);
        $dates = '2021-09-12';
        $response = $this->postUserResponse(
            'POST',
            '/development/set-daily-medal',
            [
                'user_id' => $testUser->id,
                'medals' => [
                    [
                        'type' => 'Gold',
                        'dates' => [$dates]
                    ]
                ],
                'user_id' => $testUser->id

            ]
        );
        $response->assertStatus(204);
        $response = $this->actingAs($testUser)->getUserResponse(
            'GET',
            '/achievement/medal/' . $dates . '/day'
        );
        $response->assertOk();
    }

    /** @test */
    public function testActivityForMedalsFailsOnBadData()
    {
        $response = $this->makeUser();
        $testUser = json_decode($response->getContent())->data;
        $dates = '2021-09-12';
        $response = $this->postUserResponse(
            'POST',
            '/development/set-daily-medal',
            [
                'user_id' => $testUser->id,
                'medals' => [
                    [
                        'dates' => [$dates]
                    ]
                ],
                'user_id' => $testUser->id

            ]
        );
        $response->assertStatus(422);
    }

    private function makeUser()
    {
        return $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => true,
                'role' => 4,
                'password' => 'secret',
                'status' => 'onboarded'
            ]
        );
    }
}
