<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\Activity\Models\Activity;
use Tests\Feature\ApiTest;

class ActivityStepsTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $date;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->date = date('Y-m-d');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testAddStepsWithDateTest()
    {
        $date = date('Y-m-d');
        $activity = Activity::factory()->modifier('steps', 5000, 25000)->make();

        $response = $this->postUserResponse(
            'POST',
            '/log/steps/' . $date,
            [
                'user_id' => $this->user->id,
                'type' => $activity->type,
                'steps' => $activity->value,
                'date' => $date
            ]
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
    }

    /** @test */
    public function testAddStepsWithoutDateTest()
    {
        $activity = Activity::factory()->modifier('steps', 5000, 25000)->make();

        $response = $this->postUserResponse(
            'POST',
            '/log/steps/',
            [
                'user_id' => $this->user->id,
                'type' => $activity->type,
                'steps' => $activity->value,
            ]
        );

        $this->assertDatabaseHas(
            'activity',
            [
                'user_id' => $this->user->id,
                'type' => $activity->type,
                'value' => $activity->value
            ]
        );


        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
    }

    /** @test */
    public function testAddStepsDateInPastTest()
    {
        $range = '-' . mt_rand(1, 7) . 'days';
        $date = date('Y-m-d', strtotime($range));
        $activity = Activity::factory()->modifier('steps', 5000, 25000)->make();

        $response = $this->postUserResponse(
            'POST',
            '/log/steps/' . $date,
            [
                'user_id' => $this->user->id,
                'type' => $activity->type,
                'steps' => $activity->value,
                'date' => $date
            ]
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
    }
}
