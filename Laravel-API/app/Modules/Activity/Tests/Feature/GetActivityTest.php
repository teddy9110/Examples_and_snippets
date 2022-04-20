<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Rhf\Modules\Activity\Models\Activity;
use Tests\Feature\ApiTest;

class GetActivityTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public $activity;
    protected $date;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->setupUserPreferences();
        $this->date = date('Y-m-d');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetDailyStepProgress()
    {
        $type = 'steps';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function createActivity($type)
    {
        $this->makeActivity($type);

        return Activity::create(
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'value' => $this->activity->value,
                'date' => $this->activity->date->format('Y-m-d'),
                'details' => [
                    'note' => 'Lorem Ipsum Situ Dolor',
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );
    }

    public function makeActivity($type)
    {
        $min = $this->targetMin($type);
        $max = $this->targetMax($type);

        $this->activity = Activity::factory()
        ->modifier($type, $min, $max)
        ->make();
    }

    public function testGetDailyWaterProgress()
    {
        $type = 'water';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyWeightProgress()
    {
        $type = 'weight';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyCaloriesProgress()
    {
        $type = 'calories';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyProteinProgress()
    {
        $type = 'protein';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyFatProgress()
    {
        $type = 'fat';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyFiberProgress()
    {
        $type = 'fiber';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testGetDailyCarbsProgress()
    {
        $type = 'carbohydrates';
        $this->createActivity($type);
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->addDays(7)->format('Y-m-d');

        $response = $this->getUserResponse(
            'GET',
            '/log/' . $type . '/' . $startDate . '/' . $endDate
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }

    public function testDeleteWeightLog()
    {
        $type = 'weight';
        $activity = $this->createActivity($type);

        $response = $this->getUserResponse(
            'DELETE',
            '/log/weight/' . $activity->date->format('Y-m-d')
        );

        $response->assertJson([
            'status' => 'success'
        ]);
    }

    private function targetMin(string $type)
    {
        switch ($type) {
            case 'steps':
                return 5000;
            case 'weight':
                return 95;
            case 'fat':
                return 1;
            case 'calories':
                return 1;
            case 'fiber':
                return 1;
            case 'carbohydrates':
                return 100;
            case 'protein':
                return 10;
            case 'water':
                return 1;
        }
    }

    private function targetMax(string $type)
    {
        switch ($type) {
            case 'steps':
                return 25000;
            case 'weight':
                return 495;
            case 'fat':
                return 100;
            case 'calories':
                return 5000;
            case 'fiber':
                return 200;
            case 'carbohydrates':
                return 1000;
            case 'protein':
                return 200;
            case 'water':
                return 100;
        }
    }
}
