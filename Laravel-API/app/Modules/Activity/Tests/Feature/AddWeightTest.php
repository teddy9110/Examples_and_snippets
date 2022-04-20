<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Tests\Feature\ApiTest;
use Rhf\Modules\Activity\Models\Activity;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddWeightTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $date;
    protected $activity;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->date = date('Y-m-d');
        $this->makeActivity();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testAddWeightWithDateTest()
    {
        $date = date('Y-m-d');

        $response = $this->postUserResponse(
            'POST',
            '/log/weight/' . $date,
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'weight' => $this->activity->value,
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
    public function testAddWeightWithoutDateTest()
    {
        $response = $this->postUserResponse(
            'POST',
            '/log/weight/',
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'weight' => $this->activity->value,
            ]
        );

        $this->assertDatabaseHas(
            'activity',
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'value' => $this->activity->value
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
    public function testAddWeightDateInPastTest()
    {
        $range = '-' . mt_rand(1, 7) . 'days';
        $date = date('Y-m-d', strtotime($range));

        $response = $this->postUserResponse(
            'POST',
            '/log/weight/' . $date,
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'weight' => $this->activity->value,
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


    private function makeActivity()
    {
        $this->activity = Activity::factory()
            ->modifier('weight', 95, 420)
            ->make();
    }
}
