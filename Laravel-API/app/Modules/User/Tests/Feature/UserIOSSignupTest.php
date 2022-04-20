<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\ApiTest;

class UserIOSSignupTest extends ApiTest
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
    public function testIOSUserSignUp()
    {
        $response = $this->postUserResponse(
            'POST',
            '/account/signup',
            [
                'email' => $this->user->email,
                'password' => 'password',
                'subscriptions' => true,
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'subscribed',
                'access_token',
                'token_type',
                'expires_in'
            ]
        );
    }
}
