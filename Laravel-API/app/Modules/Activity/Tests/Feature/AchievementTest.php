<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Rhf\Exceptions\FitnessBadRequestException;
use Tests\Feature\ApiTest;

class AchievementTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

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

    /** @test */
    public function testUserCanGetMedalsPerDay()
    {
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $this->date . '/day'
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success',
                'data' => [
                    'date' => $this->date,
                ]
            ]
        );
        $response->assertJsonStructure(
            [
                'status',
                'data'
            ]
        );
    }

    public function testUserCanGetMedalsPerDayFutureDateException()
    {
        $expected = $this->expectException(FitnessBadRequestException::class);
        $this->withoutExceptionHandling();

        $date = Carbon::parse($this->date)->addDays(10)->format('Y-m-d');
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $date . '/day'
        );

        $this->assertEquals($expected, $response);
    }

    public function testUserCanGetMedalPerWeek()
    {
        $date = Carbon::parse($this->date)->subWeeks(1)->format('Y-m-d');
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $date . '/week'
        );

        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success',
                'data' => []
            ]
        );
        $response->assertJsonStructure(
            [
                'status',
                'data' => []
            ]
        );
    }

    /** @test */
    public function testAchievementOverview()
    {
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/overview'
        );

        $response->assertOk();
        $response->assertJson(
            [
                'data' => []
            ]
        );
        $response->assertJsonStructure(
            [
                'data' => [
                    'last_week' => [],
                    'yesterday' => [],
                    'today' => []
                ]
            ]
        );
    }

    public function testAchievementHistorical()
    {
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/historical'
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
        ]);
    }

    /**
     * @test
     *
     * Exception Test checks if the date is beyond today
     * returns exception if true
     */
    public function testAchievementDayException()
    {
        $expected = $this->expectException(FitnessBadRequestException::class);
        $this->withoutExceptionHandling();

        $date = now()->addDay();
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $date . '/day'
        );

        $this->assertEquals($expected, $response);
    }

    /**
     * @test
     *
     * Exception Test checks if the date is beyond today
     * returns error message if true
     */
    public function testAchievementDayErrorMessage()
    {
        $date = now()->addDay();
        $response = $this->getUserResponse(
            'GET',
            '/achievement/medal/' . $date . '/day'
        );

        $this->assertEquals(
            'Invalid Date: Please select a date in the past.',
            json_decode($response->getContent())->message
        );
    }

    /**
     * @test
     *
     * Exception Test checks if the date is a previous week
     * returns exception if true
     */
    public function testAchievementWeekException()
    {
        //create user preferences
        $this->setupUserPreferences();

        $expected = $this->expectException(FitnessBadRequestException::class);
        $this->withoutExceptionHandling();

        $date = now();
        $response = $this->getUserResponse('GET', '/achievement/medal/' . $date . '/week');

        $this->assertEquals(
            'Invalid Date: Please select a previous week.',
            json_decode($response->getContent())->message
        );
        $this->assertEquals($expected, $response);
    }

    /**
     * @test
     *
     * Exception Test checks if the date is a week previous
     * returns error message
     */
    public function testAchievementWeekErrorMessage()
    {
        //create user preferences

        $date = now();
        $response = $this->getUserResponse('GET', '/achievement/medal/' . $date . '/week');

        $this->assertEquals(
            'Invalid Date: Please select a previous week.',
            json_decode($response->getContent())->message
        );
    }
}
