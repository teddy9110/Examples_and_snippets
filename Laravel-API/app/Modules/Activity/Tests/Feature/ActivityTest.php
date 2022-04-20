<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Rhf\Modules\Activity\Models\Activity;
use Tests\Feature\ApiTest;

class ActivityTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $date;
    public $activity;
    public $type;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->date = date('Y-m-d');

        $activityType = [
            'steps', 'weight', 'water',
        ];

        $this->type = $activityType[array_rand($activityType)];

        $min = $this->targetMin($this->type);
        $max = $this->targetMax($this->type);

        $this->activity = Activity::factory()
            ->modifier($this->type, $min, $max)
            ->make();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function createActivity()
    {
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

    public function createMultipleActivities(int $number = 1)
    {
        $startDate = $this->date;
        $endDate = Carbon::parse($this->date)->subDays($number)->format('Y-m-d');
        $dates = $this->getDatesBetweenPeriods($endDate, $startDate);

        foreach ($dates as $date) {
            $min = $this->targetMin($this->type);
            $max = $this->targetMax($this->type);

            Activity::factory()
                ->modifier($this->type, $min, $max)
                ->create([
                    'user_id' => $this->user->id,
                    'date' => $date,
                ]);
        }
    }

    /** @test **/
    public function testActivitiesPost()
    {
        $response = $this->postUserResponse(
            'POST',
            '/activities',
            [
                'user_id' => $this->user->id,
                'type' => $this->activity->type,
                'value' => $this->activity->value,
                'date' => $this->activity->date->format('Y-m-d'),
                'details' => [
                    'note' => "Lorem Ipsum Situ Dolor",
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
    }

    public function testActivitiesPatch()
    {
        $activity = $this->createActivity();

        $min = $this->targetMin($this->type);
        $max = $this->targetMax($this->type);

        $replaceActivity = Activity::factory()
            ->modifier($this->type, $min, $max)
            ->make();

        $response = $this->postUserResponse(
            'PATCH',
            '/activities/' . $activity->id,
            [
                'value' => $replaceActivity->value,
                'date' => $replaceActivity->date->format('Y-m-d'),
                'details' => [
                    'note' => 'Lorem Ipsum Situ Dolor - replace',
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'activity_id',
                    'type',
                    'value',
                    'date',
                    'friendly_date',
                ]
            ])
        ;
    }

    public function testActivitiesGet()
    {
        $activity = $this->createActivity();

        $response = $this->getUserResponse(
            'GET',
            '/activities',
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    public function testActivitiesGetById()
    {
        $activity = $this->createActivity();
        $response = $this->getUserResponse(
            'GET',
            '/activities/' . $activity->id
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'activity_id',
                    'type',
                    'value',
                    'date',
                    'friendly_date',
                ]
            ]);
    }

    public function testActivityDelete()
    {
        $activity = $this->createActivity();
        $response = $this->getUserResponse(
            'DELETE',
            '/activities/' . $activity->id
        );

        $response->assertStatus(204);
    }

    public function testActivitiesFiltering()
    {
        $activity = $this->createActivity();
        $date = Carbon::parse($this->date);
        $lastWeek = $date->copy()->subWeek()->format('Y-m-d');
        $today = $date->copy();

        $response = $this->actingAs($this->user, 'api')
            ->withHeaders($this->headers)
            ->json(
                'GET',
                $this->route . $this->version . '/activities',
                [
                    'type' => $activity->type,
                    'start_date' => $lastWeek,
                    'end_date' => $today,
                    'sort_by' => 'date',
                    'sort_direction' => 'desc'
                ]
            );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }

    public function testActivitiesFilteringValidation()
    {
        $activity = $this->createActivity();
        $date = Carbon::parse($this->date);
        $today = $date->copy();

        $response = $this->json(
            'GET',
            $this->route . $this->version . '/activities',
            [
                'type' => $activity->type,
                'start_date' => $today,
                'end_date' => $today,
                'sort_by' => 'date',
                'sort_direction' => 'desc'
            ]
        );

        $response->assertStatus(422);
    }

    public function testActivityAverage()
    {
        $multipleActivities = $this->createMultipleActivities(90);

        $response = $this->postUserResponse(
            'GET',
            '/activities/average/' . $this->type,
            [
                'period' => 'last-2-weeks',
                'type' => 'week'
            ]
        );

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => []
        ]);
    }

    public function testActivitiesValidation()
    {
        $response = $this->postUserResponse(
            'POST',
            '/activities',
            [
                'user_id' => $this->user->id,
                'date' => $this->activity->date->format('Y-m-d'),
                'details' => [
                    'note' => 'Lorem Ipsum Situ Dolor',
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );

        $response->assertStatus(422);
    }

    public function testActivitiesNoUserException()
    {
        $expected = $this->expectException(Exception::class);
        $this->withoutExceptionHandling();

        $response = $this->json(
            'POST',
            'api/1.0/activities',
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

        $this->assertEquals($expected, $response);
    }

    public function testActivitiesUpdateException()
    {
        $expected = $this->expectException(ModelNotFoundException::class);
        $this->withoutExceptionHandling();

        $activity = $this->createActivity();

        $min = $this->targetMin($this->type);
        $max = $this->targetMax($this->type);

        $replaceActivity = Activity::factory()
            ->modifier($this->type, $min, $max)
            ->make();

        $response = $this->postUserResponse(
            'PATCH',
            '/activities/-1',
            [
                'value' => $replaceActivity->value,
                'date' => $replaceActivity->date->format('Y-m-d'),
                'details' => [
                    'note' => 'Lorem Ipsum Situ Dolor - replace',
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );

        $this->assertEquals($expected, $response);
    }

    public function testActivityDeleteOnNoDateMatch()
    {
        $activity = $this->createActivity();

        $min = $this->targetMin($this->type);
        $max = $this->targetMax($this->type);

        $replaceActivity = Activity::factory()
            ->modifier($this->type, $min, $max)
            ->make();

        $date = $replaceActivity->date->subDay(1)->format('Y-m-d');

        $response = $this->postUserResponse(
            'PATCH',
            '/activities/' . $activity->id,
            [
                'value' => $replaceActivity->value,
                'date' => $date,
                'details' => [
                    'note' => 'Lorem Ipsum Situ Dolor - replace',
                    'period' => var_export((bool)random_int(0, 1), true)
                ]
            ]
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'activity_id',
                    'type',
                    'value',
                    'date',
                    'friendly_date',
                ]
            ]);
    }

    public function testActivitiesByIdException()
    {
        $expected = $this->expectException(ModelNotFoundException::class);
        $this->withoutExceptionHandling();

        $response = $this->getUserResponse(
            'GET',
            '/activities/-1'
        );

        $this->assertEquals($expected, $response);
    }

    public function testActivitiesDeleteException()
    {
        $expected = $this->expectException(ModelNotFoundException::class);
        $this->withoutExceptionHandling();

        $response = $this->getUserResponse(
            'DELETE',
            '/activities/-1',
        );

        $this->assertEquals($expected, $response);
    }

    private function targetMin(string $type)
    {
        switch ($type) {
            case 'steps':
                return 5000;
            case 'weight':
                return 95;
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
            case 'water':
                return 100;
        }
    }
}
