<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileTopicsOfInterestController;

use App\Models\UserProfile;
use App\Models\UserProfileTopicsOfInterest;
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
     * Ensure PUT /profile/{userId}/topics returns 401 Unauthorised when guests try to access the API
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/topics')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/topics returns 401 Unauthorised when access token has expired
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/topics')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/topics returns 401 Unauthorised when access token is invalid
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/topics')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/topics returns 401 Unauthorised when user tries to update another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorizedWhenUserTriesToUpdateAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/topics')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/topics returns 204 and the user's profile is updated
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns204AndProfileIsUpdatedWithTopicsOfInterest(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/topics', ['data' => [
                'interested_topic_codes' => ['u9009', 'u9008'],
                'uninterested_topic_codes' => ['u9007'],
            ]])->assertStatus(204);

        // check the database has been updated
        $topicsOfInterest = UserProfileTopicsOfInterest::where('user_id', 2)->whereIn('topic_code', ['u9009', 'u9008'])->where('interested', true)->get();
        self::assertCount(2, $topicsOfInterest);

        $topicsOfInterest = UserProfileTopicsOfInterest::where('user_id', 2)->whereIn('topic_code', ['u9007'])->where('interested', false)->get();
        self::assertCount(1, $topicsOfInterest);
    }

    /**
     * Ensure PUT /profile/{userId}/topics removes topics not in the request
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTOnlyRemovesOwnTopicsOfInterestThatArentInTheRequest(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        // Add a profile for another user
        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileTopicsOfInterest::factory()->count(1)->create(['user_id' => 1, 'topic_code' => 'u9009']);
        UserProfileTopicsOfInterest::factory()->count(1)->create(['user_id' => 2, 'topic_code' => 'u9008']);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/topics', ['data' => [
                'interested_topic_codes' => ['u9007'],
                'uninterested_topic_codes' => ['u9006'],
            ]])->assertStatus(204);

        // Check original topics of interest are gone
        $topicsOfInterest = UserProfileTopicsOfInterest::where('user_id', 2)->whereIn('topic_code', ['u9009', 'u9008'])->get();
        self::assertCount(0, $topicsOfInterest);

        // And new ones exists
        $topicsOfInterest = UserProfileTopicsOfInterest::where('user_id', 2)->whereIn('topic_code', ['u9007'])->where('interested', true)->get();
        self::assertCount(1, $topicsOfInterest);

        $topicsOfInterest = UserProfileTopicsOfInterest::where('user_id', 2)->whereIn('topic_code', ['u9006'])->where('interested', false)->get();
        self::assertCount(1, $topicsOfInterest);
    }

    /**
     * Ensure PUT /profile/{userId}/topics creates a new user profile if one doesn't exist
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTCreatesProfile(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/topics', ['data' => [
                'interested_topic_codes' => ['u9007'],
                'uninterested_topic_codes' => ['u9006'],
            ]])->assertStatus(204);

        // check the profile has been added to the database
        $userProfile = UserProfile::where('user_id', 2)->first();
        self::assertNotNull($userProfile);
    }
}
