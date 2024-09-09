<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileMarketingPreferencesController;

use App\Models\UserProfile;
use App\Models\UserProfileLearningProvider;
use App\Models\MarketingPreference;
use App\Models\UserMarketingPreference;
use App\Support\Graph;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use TSR\EventBridgeIngestion\Publisher;
use Mockery;

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

        $memberClaim = $guestClaim;
        $memberClaim['id'] = 2;
        $memberClaim['user_id'] = 2;
        $memberClaim['username'] = 'test_user';

        $expiredClaim = $guestClaim;
        $expiredClaim['iat'] = Carbon::now()->subHour()->timestamp;
        $expiredClaim['exp'] = Carbon::now()->subHour()->timestamp;

        $this->guestJWTToken = 'Bearer ' . JWT::encode($guestClaim, config('jwt.secret'), config('jwt.algo'));
        $this->memberJWTToken = 'Bearer ' . JWT::encode($memberClaim, config('jwt.secret'), config('jwt.algo'));
        $this->expiredJWTToken = 'Bearer ' . JWT::encode($expiredClaim, config('jwt.secret'), config('jwt.algo'));

        $this->invalidJWTToken = 'Bearer ' . JWT::encode($guestClaim, 'invalid', config('jwt.algo'));

        $graphMock = Mockery::mock(Graph::class)->makePartial();
        $graphMock->shouldReceive('getUserFirstPartyTopics')->andReturn(['u9007', 'u9008', 'u9009']);

        app()->bind("Graph", function () use ($graphMock) {
            return $graphMock;
        });
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/marketing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/marketing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/marketing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersMarketingProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/marketing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 204 and the user's profile is updated
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns204AndMarketingPreferencesAreUpdated(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/marketing_preferences', [
                'data' => [
                    [
                        "code" => "B2B2C_PARTNERS",
                        "value" => "WEEKLY"
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $partnersId = MarketingPreference::where('name','Brand Partners')->first()->id;
        $userMarketingPreference  = UserMarketingPreference::where('user_id', 2)->where('marketing_preference_id', $partnersId)->first();
        $this->assertEquals(2, $userMarketingPreference->user_id);
        $this->assertEquals(3, $userMarketingPreference->marketing_preference_id);
        $this->assertEquals('WEEKLY', $userMarketingPreference->frequency);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 204 and the user's profile is updated
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns204AndMarketingPreferencesAreUpdatedWhenStudentLoansIsPassedIn(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/marketing_preferences', [
                'data' => [
                    [
                        "code" => "B2B2C_STUDENT_LOANS",
                        "value" => "WEEKLY"
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $studentLoansId = MarketingPreference::where('name','Student Loans')->first()->id;
        $userMarketingPreference  = UserMarketingPreference::where('user_id', 2)->where('marketing_preference_id', $studentLoansId)->first();
        $this->assertEquals(2, $userMarketingPreference->user_id);
        $this->assertEquals(2, $userMarketingPreference->marketing_preference_id);
        $this->assertEquals('WEEKLY', $userMarketingPreference->frequency);
    }

    /**
     * Ensure PUT /profile/{userId}/MarketingPreferences returns 400 and nothing us updated when bad data passed in
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturnsDatabaseErrorOnInvalidMarketingPreference(): void
    {
        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson(
                $this->apiPrefix . '/profile/2/marketing_preferences',
                [
                    'data' => [
                        [
                            "code" => "phone",
                            "value" => "WEEKLY"
                        ]
                    ]
                ]
            )->assertStatus(400);
    }
}
