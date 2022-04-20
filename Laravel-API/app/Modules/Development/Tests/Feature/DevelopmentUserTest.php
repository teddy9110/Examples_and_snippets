<?php

namespace Rhf\Modules\Development\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\UserRole;
use Tests\Feature\ApiTest;

class DevelopmentUserTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupUser();
        $this->setupExercise();
    }

    /** @test */
    public function testThatTestUserCanBeCreatedWithoutActivities()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => true,
                'role' => 4,
                'password' => 'secret',
                'status' => 'not_onboarded'
            ]
        );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'staff_user',
                    'has_paid',
                    'expire_at',
                ]
            ]);
    }

    /** @test */
    public function testThatTestUserCanBeCreatedWithActivities()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => true,
                'role' => 4,
                'password' => 'secret',
                'status' => 'onboarded'
            ]
        );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'staff_user',
                    'has_paid',
                    'expire_at',
                ]
            ]);
    }

    /** @test */
    public function testThatInvalidRoleIsCaught()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => true,
                'role' => 10,
                'password' => 'secret',
                'status' => 'onboarded'
            ]
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'role'
        ]);
    }

    /** @test */
    public function testThatInvalidPaidIsCaught()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => 4,
                'role' => 4,
                'password' => 'secret',
                'status' => 'onboarded'
            ]
        );
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'paid'
        ]);
    }

    /** @test */
    public function testThatUserCanBeDeleted()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-seeded-user',
            [
                'paid' => true,
                'role' => 4,
                'password' => 'secret',
                'status' => 'not_onboarded'
            ]
        );

        $user = json_decode($response->getContent())->data;

        $response = $this->postUserResponse(
            'POST',
            '/development/delete-seeded-users',
            [
                'user_id' => [$user->id]
            ]
        );
        $response->assertStatus(204);
    }
}
