<?php

namespace Rhf\Modules\Competition\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Rhf\Modules\Competition\Models\Competition;
use Rhf\Modules\Competition\Models\CompetitionEntry;
use Rhf\Modules\Competition\Models\CompetitionReports;
use Rhf\Modules\Competition\Models\CompetitionVotes;
use Tests\Feature\ApiTest;

class EntryTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $date;
    public $competition;
    public $entry;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->date = date('Y-m-d');

        $this->competition = Competition::factory()->create([
            'start_date' => Carbon::parse($this->date)->subWeek(),
            'end_date' => Carbon::parse($this->date)->addWeeks(2)
        ]);
        $this->entry = CompetitionEntry::factory()->make([
            'image' => UploadedFile::fake()->create(Str::random(25) . '.jpg')
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function createUserEntry()
    {
        return CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id
        ]);
    }

    public function closeCompetition(Competition $competition)
    {
        $competition->closed = 1;
        $competition->save();
    }

    public function expireCompetition(Competition $competition)
    {
        $competition->active = 0;
        $competition->save();
    }

    public function testCompetitionEntry()
    {
        $response = $this->postUserResponse(
            'POST',
            '/competitions/' . $this->competition->id . '/entries',
            [
                'user_id' => $this->user->id,
                'image' => $this->entry->image,
                'description' => $this->entry->description
            ]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'image_url',
                    'vote_count',
                    'share_url',
                    'competition' => [
                        'id',
                        'title',
                        'status'
                    ],
                    'submitted_by' => [
                        'name',
                        'forename',
                        'surname',
                        'email'
                    ],
                    'created_at',
                    'date_created'
                ]
            ]);
    }

    public function testCompetitionEntryClosedCompetition()
    {
        $this->closeCompetition($this->competition);

        $response = $this->postUserResponse(
            'POST',
            '/competitions/' . $this->competition->id . '/entries',
            [
                'title' => $this->competition->title,
                'competition_id' => $this->competition->id,
                'user_id' => $this->user->id,
                'image' => $this->entry->image,
                'description' => $this->entry->description
            ]
        );
        $response->assertStatus(400)
            ->assertSeeText('Unable to submit an entry to an expired competition.');
    }

    public function testCompetitionEntryExpiredCompetition()
    {
        $this->expireCompetition($this->competition);

        $response = $this->postUserResponse(
            'POST',
            '/competitions/' . $this->competition->id . '/entries',
            [
                'title' => $this->competition->title,
                'competition_id' => $this->competition->id,
                'user_id' => $this->user->id,
                'image' => $this->entry->image,
                'description' => $this->entry->description
            ]
        );

        $response->assertStatus(400)
            ->assertSeeText('Unable to submit an entry to an expired competition.');
    }

    public function testCompetitionMultipleEntriesCompetition()
    {
        $originalEntry = CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id
        ]);
        $entry = CompetitionEntry::factory()->make([
            'image' => UploadedFile::fake()->create(Str::random(25) . '.jpg')
        ]);

        $response = $this->postUserResponse(
            'POST',
            '/competitions/' . $this->competition->id . '/entries',
            [
                'title' => $this->competition->title,
                'competition_id' => $this->competition->id,
                'user_id' => $this->user->id,
                'image' => $this->entry->image,
                'description' => $this->entry->description
            ]
        );

        $response->assertStatus(400)
            ->assertSeeText('Unable to submit more than a single entry to the competition.');
    }

    public function testGetUsersCompetitionEntry()
    {
        $response = $this->getUserResponse(
            'GET',
            '/competitions/user-entries'
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
            ]);
    }

    public function testGetCompetitionEntry()
    {
        $entry = CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id,
            'reports' => 1,
            'votes' => 1,
        ]);

        $response = $this->getUserResponse(
            'GET',
            '/competition-entries/' . $entry->id
        );

        $response->assertStatus(200);
    }

    public function testCompetitionEntryEdit()
    {
        $entry = CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id
        ]);

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/edit',
            [
                'description' => $entry->description . ' - edit'
            ]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'image_url',
                    'vote_count',
                    'share_url',
                    'competition' => [
                      'id',
                      'title',
                      'status'
                    ],
                    'submitted_by' => [
                      'name',
                      'forename',
                      'surname',
                      'email'
                    ],
                    'created_at',
                    'date_created'
                ]
            ]);
    }

    public function testCompetitionEntryDelete()
    {
        $entry = $this->createUserEntry();
        $response = $this->getUserResponse(
            'DELETE',
            '/competition-entries/' . $entry->id
        );

        $response->assertStatus(204);
    }

    public function testCompetitionEntryWithBodyTestReport()
    {
        $entry = $this->createUserEntry();
        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/report',
            [
                'report' => 'Report entry'
            ]
        );
        $response->assertStatus(204);
    }

    public function testCompetitionEntryWithoutBodyTestReport()
    {
        $entry = $this->createUserEntry();

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/report',
            []
        );

        $response->assertStatus(204);
    }

    public function testCompetitionEntryMultipleSingleUserReports()
    {
        $entry = $this->createUserEntry();
        $report = CompetitionReports::create([
            'entry_id' => $entry->id,
            'user_id' => $this->user->id,
            'report' => 'Report entry'
        ]);

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/report',
            [
                'report' => 'Report entry'
            ]
        );
        $response->assertStatus(400)
            ->assertSeeText('Sorry, you can only report an entry once');
    }

    public function testCompetitionEntrySuspendViaReports()
    {
        $entry = $this->createUserEntry();
        $entry->update([
            'reports' => 9
        ]);

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/report',
            [
                'report' => 'Report entry'
            ]
        );
        $this->assertDatabaseHas('competition_entries', [
            'suspended' => true
        ]);
        $response->assertStatus(204);
    }

    public function testCompetitionEntryAddVote()
    {
        $entry = $this->createUserEntry();
        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/vote',
            []
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ])
            ->assertJsonStructure([
                'data' => [
                    'votes',
                    'voted'
                ]
            ]);
    }

    public function testCompetitionEntryAddVoteClosedCompetition()
    {
        $entry = $this->createUserEntry();
        $this->closeCompetition($this->competition);

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/vote',
            []
        );

        $response->assertStatus(400)
            ->assertSeeText('Sorry, you are unable to vote on an expired competition or revoked entry');
    }

    public function testCompetitionEntryRemoveVote()
    {
        $entry = CompetitionEntry::factory()->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id
        ]);

        $addVote = CompetitionVotes::create([
            'user_id' => $this->user->id,
            'entry_id' => $entry->id,
        ]);

        $response = $this->postUserResponse(
            'POST',
            '/competition-entries/' . $entry->id . '/vote',
            []
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ])
            ->assertJsonStructure([
                'data' => [
                    'votes',
                    'voted'
                ]
            ]);
    }

    public function testUserCompetitionEntries()
    {
        $entry = $this->createUserEntry();

        $response = $this->getUserResponse(
            'GET',
            '/competition-entries'
        );

        $response->assertOk()
            ->assertJson([
                'data' => []
        ]);
    }

    public function testCompetitionsEntriesPagination()
    {
        $entries = CompetitionEntry::factory(10)->create([
            'user_id' => $this->user->id,
            'title' => $this->competition->title,
            'competition_id' => $this->competition->id
        ]);

        $response = $this->json(
            'GET',
            $this->route . $this->version . '/competitions/' . $this->competition->id . '/entries',
            [
                'page' => 2,
                'limit' => 3,
                'sort_by' => 'votes',
                'sort_direction' => 'desc',
                'include' => 'suspended'
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ]);
    }
}
