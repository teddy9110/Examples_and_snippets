<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\ApiTest;

class UserProgressConsentTest extends ApiTest
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setUserActive();
        $this->setupUserPreferences();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
    /** @test */
    public function testUserCanConsentToProgressPictures()
    {
        $type = rand(0, 1) === 1 ? "accepted" : "rejected";
        $response = $this->postUserResponse(
            'POST',
            '/account/progress/consent/' . $type,
            []
        );
        $response->assertStatus(204);
    }
}
