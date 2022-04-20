<?php

namespace Rhf\Modules\Exercise\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Exercise\Models\Exercise;
use Tests\Feature\ApiTest;

class ExerciseTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public $exercise;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->exercise = Exercise::factory()->create();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function testUserCanPostExerciseWithDate()
    {
        $date = now()->format('Y-m-d');
        $response = $this->postUserResponse(
            'POST',
            '/log/exercise/' . $date,
            [
                'exercise_id' => $this->exercise->id
            ]
        );
        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
        $response->assertJsonStructure(
            [
                'status'
            ]
        );
    }

    /** @test */
    public function testUserCanPostExerciseWithoutDate()
    {
        $response = $this->postUserResponse(
            'POST',
            '/log/exercise/',
            [
                'exercise_id' => $this->exercise->id
            ]
        );
        $response->assertOk();
        $response->assertJson(
            [
                'status' => 'success'
            ]
        );
        $response->assertJsonStructure(
            [
                'status'
            ]
        );
    }

    /**
     * @test
     *
     * User cannot add exercise in advance
     * Should return error message
     */
    public function testUserCannotPostExerciseInAdvanceError()
    {
        $date = now()->add('week', 1)->format('Y-m-d');
        $response = $this->postUserResponse(
            'POST',
            '/log/exercise/' . $date,
            [
                'exercise_id' => $this->exercise->id
            ]
        );
        $this->assertEquals(
            'Error: date is not valid or too far in the future.',
            json_decode($response->getContent())->message
        );
    }

    /**
     * @test
     *
     * User cannot add exercise in advance
     * Should return Exception message
     */
    public function testUserCannotPostExerciseInAdvanceException()
    {
        $expected = $this->expectException(FitnessBadRequestException::class);
        $this->withoutExceptionHandling();
        $date = now()->add('week', 1)->format('Y-m-d');
        $response = $this->postUserResponse(
            'POST',
            '/log/exercise/' . $date,
            [
                'exercise_id' => $this->exercise->id
            ]
        );
        $this->assertEquals($expected, $response);
    }

    /** @test */
    public function testUserCanGetCategories()
    {
        $response = $this->getUserResponse('GET', '/exercise-categories');

        $response->assertStatus(200);
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
    public function testUserCanGetLevels()
    {
        $response = $this->getUserResponse('GET', '/exercise-levels');

        $response->assertStatus(200);
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
}
