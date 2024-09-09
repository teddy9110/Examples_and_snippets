<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileSubjectController;

use App\Models\UserProfile;
use App\Models\UserProfileSubject;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\TSRGJWT;
use App\Support\Graph;
use Mockery;
use TSR\EventBridgeIngestion\Publisher;

class PutTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    protected $guestJWTToken;

    /**
     * @var string
     */
    protected $memberJWTToken;

    /**
     * @var string
     */
    protected $expiredJWTToken;

    /**
     * @var string
     */
    protected $invalidJWTToken;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();

        config()->set('audit.console', true);
        $guestClaim = [
            'iss' => 'https://www.thestudentroom.com',
            'iat' => Carbon::now()->timestamp,
            'type' => 'access',
            'exp' => Carbon::now()->addHour()->timestamp,
            'id' => 1, // Separate from user_id as it's just a unique identifer for token
            'user_id' => 0,
            'forum' => [
                'user_group_id' => 1,
                'member_group_ids' => [],
                'infraction_group_ids' => [],
                'permissions' => [
                    'forums' => 8917007,
                ],
            ],
        ];

        $jwtClass = new TSRGJWT(json_decode(json_encode([
            'user_id' => 1,
            'userGroupId' => 143,
            'forum' => [
                'user_group_id' => 143,
                'member_group_ids' => [143],
                'infraction_group_ids' => [],
                ]
            ]
        ), false));

        app()->instance(TSRGJWT::class, $jwtClass);
        $memberClaim = $guestClaim;
        $memberClaim['id'] = 2;
        $memberClaim['user_id'] = 2;
        $memberClaim['username'] = 'test_user';

        $expiredClaim = $guestClaim;
        $expiredClaim['iat'] = Carbon::now()->subHour()->timestamp;
        $expiredClaim['exp'] = Carbon::now()->subHour()->timestamp;

        $this->guestJWTToken = 'Bearer '.JWT::encode($guestClaim, config('jwt.secret'), config('jwt.algo'));
        $this->memberJWTToken = 'Bearer '.JWT::encode($memberClaim, config('jwt.secret'), config('jwt.algo'));
        $this->expiredJWTToken = 'Bearer '.JWT::encode($expiredClaim, config('jwt.secret'), config('jwt.algo'));

        $this->invalidJWTToken = 'Bearer '.JWT::encode($guestClaim, 'invalid', config('jwt.algo'));

        $graphMock = Mockery::mock(Graph::class)->makePartial();
        $graphMock->shouldReceive('getUserFirstPartyTopics')->andReturn(['u9007', 'u9008', 'u9009']);

        app()->bind("Graph", function () use ($graphMock) {
            return $graphMock;
        });
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{subject} returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/subject/current', ['data' => [
                'previous' => false,
                'current' => true,
                'future' => false
            ]])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{subject} returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/subject/current', ['data' => [
                'previous' => false,
                'current' => true,
                'future' => false
            ]])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{subject} returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/subject/current', ['data' => [
                'previous' => false,
                'current' => true,
                'future' => false
            ]])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{subject} returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/subject/current', ['data' => [
                'previous' => false,
                'current' => true,
                'future' => false
            ]])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{subject} returns 204 and the user's profile is updated
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns204AndProfileIsUpdated(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileSubject::factory()->count(1)->create([
            'user_id' => 2,
            'subject_id' => 2,
            'previous' => true,
            'current' => false,
            'future' => false,
            'predicted_grade' => 'A',
        ]);


        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/subject/current', ['data' => [
                "subject_ids" => [3]
            ]])->assertStatus(204);

        // check the provided fields have been updated
        $subject = UserProfileSubject::where('subject_id', 3)->first();
        self::assertCount(1, $subject->audits);
        self::assertEquals(2, $subject->audits->first()->user_id);
        self::assertEquals($subject['user_id'], 2);
        self::assertEquals($subject['previous'], false);
        self::assertEquals($subject['current'], true);
        self::assertEquals($subject['future'], false);
        self::assertEquals($subject['predicted_grade'], null);
    }


    /**
     * Ensure PUT /profile/{userId}/subject/{stage} only updates / deletes own users data
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTOnlyRemovesOwnUserssubjectsThatArentInTheRequest(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        // Add a profile for another user
        UserProfile::factory()->count(1)->create(['user_id' => 1]);
        UserProfileSubject::factory()->count(1)->create(['user_id' => 1, 'subject_id' => 5, 'current' => true, 'previous' => false, 'future' => false]);

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileSubject::factory()->count(1)->create(['user_id' => 2, 'subject_id' => 5, 'current' => true, 'previous' => false, 'future' => false]);
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/subject/current', ['data' => [
                'subject_ids' => [3,4],
            ]])->assertStatus(204);

        // Get users learning providers
        $subjects = UserProfileSubject::where('user_id', 2)->orderBy('subject_id', 'asc')->get();
        self::assertCount(2, $subjects);

        // Check first learning provider
        self::assertEquals($subjects[0]['previous'], false);
        self::assertEquals($subjects[0]['current'], true);
        self::assertEquals($subjects[0]['future'], false);
        self::assertEquals($subjects[0]['user_id'], 2);
        self::assertEquals($subjects[0]['subject_id'], 3);

        // Check second learning provider
        self::assertEquals($subjects[1]['previous'], false);
        self::assertEquals($subjects[1]['current'], true);
        self::assertEquals($subjects[1]['future'], false);
        self::assertEquals($subjects[1]['user_id'], 2);
        self::assertEquals($subjects[1]['subject_id'], 4);

        // Get other users learning providers
        $subjects = UserProfileSubject::where('user_id', 1)->orderBy('subject_id', 'asc')->get();
        self::assertCount(1, $subjects);

        // Check first learning provider
        self::assertEquals($subjects[0]['previous'], false);
        self::assertEquals($subjects[0]['current'], true);
        self::assertEquals($subjects[0]['future'], false);
        self::assertEquals($subjects[0]['user_id'], 1);
        self::assertEquals($subjects[0]['subject_id'], 5);
    }

    /**
     * Ensure PUT /profile/{userId}/subject/{stage} creates a new user profile if one doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturnsCorrectContent(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        // check the profile does not exist
        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $subject = UserProfileSubject::find(3);
        self::assertNull($subject);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/subject/current', ['data' => [
                "subject_ids" => [3]
            ]])->assertStatus(204);

        // check the profile has been added to the database
        $subject = UserProfileSubject::where('subject_id', 3)->first();
        self::assertCount(1, $subject->audits);
        self::assertEquals(2, $subject->audits->first()->user_id);
        self::assertEquals($subject['user_id'], 2);
        self::assertEquals($subject['subject_id'], 3);
        self::assertEquals($subject['previous'], false);
        self::assertEquals($subject['current'], true);
        self::assertEquals($subject['future'], false);
        self::assertEquals($subject['predicted_grade'], null);
    }
}
