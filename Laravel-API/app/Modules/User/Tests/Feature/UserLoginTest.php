<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\ApiTest;

class UserLoginTest extends ApiTest
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserExists()
    {
        $this->assertDatabaseHas(
            'users',
            [
                'email' => $this->user->email
            ]
        );
    }

    /** @test */
    public function testUserDoesNotHavePreferences()
    {
        $this->assertDatabaseMissing(
            'user_preferences',
            [
                'user_id' => $this->user->id
            ]
        );
    }

    /** @test */
    public function testUserHasPreferences()
    {
        if (is_null($this->user->preferences)) {
            $this->user->preferences()->create();
        }

        $this->assertDatabaseHas(
            'user_preferences',
            [
                'user_id' => $this->user->id
            ]
        );
    }

    //** @test */
    public function testUserCanLogin()
    {
        $this->setUserActive();
        $response = $this->actingAs($this->user, 'api')
            ->withHeaders($this->headers)
            ->json(
                'POST',
                $this->route . $this->version . '/auth/login',
                [
                    'email' => $this->user->email,
                    'password' => 'password',
                ]
            );

        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'access_token',
                'token_type',
                'expires_in'
            ]
        );
    }
}
