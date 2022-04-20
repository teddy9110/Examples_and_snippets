<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\Activity\Models\Activity;
use Tests\Feature\ApiTest;

class ActivityWaterTest extends ApiTest
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

    /**
     * Test for logging water
     */
    public function testPostWaterActivityLog()
    {
        $activity = $this->makeActivity();

        $user = $this->user;
        $this->setUserActive();

        $response = $this->postUserResponse(
            'POST',
            '/log/water/' . $this->date,
            [
                'glasses_of_water' => $activity->value,
                'calculation_type' => 'append'
            ]
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
    }

    public function testReplaceWaterActivityLog()
    {
        $activity = $this->makeActivity();

        $user = $this->user;

        $response = $this->actingAs($user, 'api')
            ->withHeaders($this->headers)
            ->json(
                'POST',
                $this->route . $this->version . '/log/water/' . $this->date,
                [
                    'user_id' => $user->id,
                    'type' => $activity->type,
                    'glasses_of_water' => $activity->value,
                    'date' => $this->date,
                    'calculation_type' => 'replace'
                ]
            );

        $response
            ->assertStatus(200)
            ->assertJson([
                 'status' => 'success'
             ]);
    }

    /** @test */
    public function testAddWaterDateInPastTest()
    {
        $range = '-' . mt_rand(1, 7) . 'days';
        $date = date('Y-m-d', strtotime($range));
        $activity = $this->makeActivity();

        $response = $this->postUserResponse(
            'POST',
            '/log/water/' . $date,
            [
                'user_id' => $this->user->id,
                'type' => $activity->type,
                'glasses_of_water' => $activity->value,
                'date' => $date,
                'calculation_type' => 'append'
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
        $activity = Activity::factory()
            ->modifier('water', 4, 20)
            ->make();
        return $activity;
    }
}
