<?php

namespace Rhf\Modules\Competition\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Competition\Models\Competition;
use Rhf\Modules\Competition\Models\CompetitionEntry;
use Tests\Feature\ApiTest;

class CompetitionTest extends ApiTest
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

    public function testActiveCompetition()
    {
        $response = $this->getUserResponse(
            'GET',
            '/competitions'
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
            ]);
    }

    public function testCompetitionLatest()
    {
        $response = $this->json(
            'GET',
            $this->route . $this->version . '/competitions',
            [
                'type' => 'latest',
            ]
        );

        $response->assertOk()
            ->assertJson([
              'data' => []
            ]);
    }

    public function testCompetitionsPrevious()
    {
        $response = $this->json(
            'GET',
            $this->route . $this->version . '/competitions',
            [
                'type' => 'previous',
                'page' => 1,
                'limit' => 10
            ]
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
            ]);
    }

    public function testCompetitionPagination()
    {
        $response = $this->json(
            'GET',
            $this->route . $this->version . '/competitions',
            [
                'type' => 'previous',
                'page' => 1,
                'limit' => 10,
                'sort_by' => 'created_at',
                'sort_direction' => 'desc'
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }
    public function testCompetitionByIdException()
    {
        $expected = $this->expectException(FitnessBadRequestException::class);
        $this->withoutExceptionHandling();

        $response = $this->getUserResponse(
            'GET',
            '/competitions/1'
        );

        $this->assertEquals($expected, $response);
    }

    public function testCompetitionById()
    {
        $competition = Competition::factory()->create();

        $response = $this->getUserResponse(
            'GET',
            '/competitions/' . $competition->id
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
        ]);
    }

    public function testCompetitionEntries()
    {
        $competition = Competition::factory()->create();
        $entry = CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $competition->title,
            'competition_id' => $competition->id
        ]);

        $response = $this->getUserResponse(
            'GET',
            '/competitions/' . $competition->id . '/entries'
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
            ]);
    }
}
