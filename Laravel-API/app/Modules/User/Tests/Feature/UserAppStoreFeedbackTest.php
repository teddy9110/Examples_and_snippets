<?php

namespace Rhf\Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\ApiTest;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserAppStoreFeedbackTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public $userPreferences;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setUserToDefaultName();
        $this->setUserActive();
        $this->setupExercise();
        $this->setupUserPreferences();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserIsEligibleForReview()
    {
        $this->setUserEligible();

        $response = $this->getUserResponse('GET', '/app-review');
        $this->assertAuthenticated('api');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'present_review_dialog' => true,
                ]
            ]
        );
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'present_review_dialog',
                    'next_review_request',
                    'last_review_submitted',
                    'user_response',
                ]
            ]
        );
    }

    /** @test */
    public function testUserNotEligible()
    {
        $this->setUserIneligible();
        $response = $this->getUserResponse('GET', '/app-review');
        $this->assertAuthenticated('api');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'present_review_dialog' => false,
                ]
            ]
        );
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'present_review_dialog',
                    'next_review_request',
                    'last_review_submitted',
                    'user_response'
                ]
            ]
        );
    }

    /** @test */
    public function testUserFeedbackTopics()
    {
        $this->setupTopics();
        $response = $this->getUserResponse('GET', '/app-feedback-topics');
        $this->assertAuthenticated('api');
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    [
                        'id',
                        'title',
                        'slug',
                    ]
                ]
            ]
        );
    }

    /** @test */
    public function testSendPopUpResponse()
    {
        $response = $this->postUserResponse(
            'POST',
            '/app-review',
            [
                'user_response' => 'dismiss',
            ]
        );
        $this->assertAuthenticated('api');
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'present_review_dialog',
                    'next_review_request',
                    'last_review_submitted',
                    'user_response',
                ]
            ]
        );
    }

    /** @test */
    public function testSendInvalidPopUpResponse()
    {

        $response = $this->postUserResponse(
            'POST',
            '/app-review',
            [
                'user_response' => 'Testing12345',
            ]
        );
        $this->assertAuthenticated('api');
        $response->assertStatus(422);
    }

    /** @test */
    public function testSendFeedback()
    {
        $this->setupTopics();
        $response = $this->postUserResponse(
            'POST',
            '/app-review-feedback',
            [
                'score' => 0,
                'feedback_topics' => [
                    'recipes',
                ],
                'comments' => 'blah blah blah'
            ]
        );
        $this->assertAuthenticated('api');
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'score',
                    'comments',
                    'feedback_topics' => [
                        [
                            'id',
                            'title',
                            'slug',
                        ],
                    ]
                ]
            ]
        );
    }
    /** @test */
    public function testSendInvalidFeedback()
    {
        $response = $this->postUserResponse(
            'POST',
            '/app-review-feedback',
            [
                'score' => 0,
                'feedback_topics' => [
                    1,
                ],
                'comments' => 'blah blah blah'
            ]
        );
        $this->assertAuthenticated('api');
        $response->assertStatus(422);
    }
}
