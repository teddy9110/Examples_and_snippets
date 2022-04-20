<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\User;
use Tests\Feature\ApiTest;

class UserTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public $user;

    //set api headers
    public $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    //api route / version
    public $route = '/api/';
    public $version = '1.0';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->make();
    }

    public function createPendingUser()
    {
        return $this->json(
            'POST',
            $this->route . $this->version . '/user/new-pending',
            [
                'email' => $this->user->email
            ]
        );
    }

    public function testCreatePendingUser()
    {
        $response = $this->createPendingUser();

        $response->assertStatus(200)
            ->assertSeeText('success');
    }

    public function testUserAccountStatusNotActive()
    {
        $createUser = $this->createPendingUser();

        $response = $this->json(
            'POST',
            $this->route . $this->version . '/account/status',
            [
                'email' => $this->user->email
            ]
        );

        $response->assertStatus(200)
            ->assertSeeText('not_active');
    }

    public function testCanGetUsersAccountDetails()
    {
        $createUser = $this->createPendingUser();
        $resp = $this->withHeaders($this->headers)
            ->json(
                'POST',
                $this->route . $this->version . '/account/signup',
                [
                    'email' => $this->user->email,
                    'password' => 'secret'
                ]
            );
        $token = json_decode($resp->getContent(), true);
        $this->headers['access_token'] = $token['access_token'];
        $response = $this
            ->withHeaders($this->headers)
            ->json(
                'GET',
                $this->route . $this->version . '/account/details'
            );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }
}
