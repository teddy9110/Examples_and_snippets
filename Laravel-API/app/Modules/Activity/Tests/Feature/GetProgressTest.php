<?php

namespace Rhf\Modules\Activity\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Rhf\Modules\User\Models\User;
use Tests\Feature\ApiTest;

class GetProgressTest extends ApiTest
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

    public function testDailyProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/daily/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testDailyFiberProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/fiber/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testDailyProteinProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/protein/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testDailyCaloriesProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/calories/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testDailyStepsProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/steps/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testDailyWaterProgress()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/water/' . $this->date
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testProgressWeightLoss()
    {
        $response = $this->getUserResponse(
            'GET',
            '/progress/weight-loss'
        );

        $response->assertJson([
            'status' => 'success',
            'data' => []
        ]);
    }

    public function testProgressPictureUpload()
    {
        $user = User::findOrFail($this->user->id);

        $data = [
            'items' => [
                'side' => [
                    'type' => 'side',
                    'file' =>  UploadedFile::fake()->image(Str::random('22') . '.jpg', 1024, 1024)->size(900)
                ],
                'front' => [
                    'type' => 'front',
                    'file' =>  UploadedFile::fake()->image(Str::random('22') . '.jpg', 1024, 1024)->size(900)
                ],
            ],
            'date' => now()->format('d-m-Y'),
        ];

        $result = $this->postUserResponse(
            'POST',
            '/account/progress',
            $data
        );

        $result->assertStatus(201)
            ->assertJson([
            'data' => []
        ]);
    }
}
