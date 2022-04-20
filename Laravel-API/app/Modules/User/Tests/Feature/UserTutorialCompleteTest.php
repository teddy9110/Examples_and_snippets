<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\ApiTest;

class UserTutorialCompleteTest extends ApiTest
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
    /**
     * @test
     */
    public function testUserCanCompleteTutorial()
    {
        $response = $this->postUserResponse(
            'POST',
            '/account/tutorial',
            []
        );
        $response->assertOk();
    }
}
