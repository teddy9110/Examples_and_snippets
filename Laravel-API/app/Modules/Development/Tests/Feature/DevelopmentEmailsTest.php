<?php

namespace Rhf\Modules\Development\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\User;
use Tests\Feature\ApiTest;
use Illuminate\Support\Facades\Auth;

class DevelopmentEmailsTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupUser();
    }
    /** @test */
    public function testThatEmailsAreSent()
    {
        $response = $this->getUserResponse(
            'get',
            '/development/send-in-blue-emails/' .  $this->user->id,
        );
        $response->assertStatus(204);
    }
}
