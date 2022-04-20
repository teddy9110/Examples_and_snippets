<?php

namespace Rhf\Modules\Development\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\UserRole;
use Tests\Feature\ApiTest;

class DevelopmentVideoTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupUser();
    }

    public function testThatVideoCanBeCreated()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-video',
            [
                'title' => 'test',
                'description' => 'test1',
                'url' => 'https://www.youtube.com/watch?v=Fv6Qpw9i7gc',
                'scheduled_date' => Carbon::now()->addDays(rand(1, 7))->format('Y-m-d'),
                'scheduled_time' => Carbon::now()->addHours(rand(1, 24))->format('h:m'),
                'active' => false
            ]
        );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'url',
                    'thumbnail',
                    'open_count',
                    'view_count',
                    'live',
                    'tags',
                    'scheduled_date',
                    'scheduled_time',
                    'active',
                    'order',
                ]
            ]);
    }

    public function testThatInvalidDateIsCaught()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-video',
            [
                'title' => 'test',
                'description' => 'test1',
                'url' => 'https://www.youtube.com/watch?v=Fv6Qpw9i7gc',
                'scheduled_date' => '2021-88-01',
                'scheduled_time' => '10:24',
                'active' => false
            ]
        );

        $response->assertStatus(422);
    }

    public function testThatInvalidTimeIsCaught()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-video',
            [
                'title' => 'test',
                'description' => 'test1',
                'url' => 'https://www.youtube.com/watch?v=Fv6Qpw9i7gc',
                'scheduled_date' => '2021-08-01',
                'scheduled_time' => '26:24',
                'active' => false
            ]
        );

        $response->assertStatus(422);
    }

    public function testThatInvalidUrlIsCaught()
    {
        $response = $this->postUserResponse(
            'POST',
            '/development/create-video',
            [
                'title' => 'test',
                'description' => 'test1',
                'url' => 'https://youtu.be/4ht22ReBjno',
                'scheduled_date' => '2021-08-01',
                'scheduled_time' => '26:24',
                'active' => false
            ]
        );

        $response->assertStatus(422);
    }
}
