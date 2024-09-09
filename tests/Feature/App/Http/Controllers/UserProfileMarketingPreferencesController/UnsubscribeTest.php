<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileMarketingPreferencesController;

use App\Models\UserProfile;
use App\Models\UserMarketingPreference;
use App\Support\Graph;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use TSR\EventBridgeIngestion\Publisher;
use Mockery;

class UnsubscribeTest extends TestCase
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
     * Ensure PUT profile/marketing_preferences/unsubscribe returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->putJson($this->apiPrefix . '/profile/marketing_preferences/unsubscribe')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PUT profile/marketing_preferences/unsubscribe returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->putJson($this->apiPrefix . '/profile/marketing_preferences/unsubscribe')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PUT profile/marketing_preferences/unsubscribe returns 401 Unauthorised when user tries to unsubscribe another user
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorizedWhenUserTriesToUnsubAnotherUsers(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/marketing_preferences/unsubscribe', ['data' => ['user_id' => 'dUREa2VvMUw2RjA9', 'marketing_preferences_code' => 'B2C_NEWSLETTER']])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT profile/marketing_preferences/unsubscribe returns 400 if missing params
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTReturns400IfMissingParams(): void
    {
        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $response = $this->putJson(
            $this->apiPrefix . '/profile/marketing_preferences/unsubscribe',
            [
                'data' => []
            ]
        )->assertStatus(400);
    }

    /**
     * Ensure PUT profile/marketing_preferences/unsubscribe returns 204
     *
     * @return void
     * 
     * @throws \JsonException
     */
    public function testPUTReturns204(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $response = $this->putJson(
            $this->apiPrefix . '/profile/marketing_preferences/unsubscribe',
            [
                'data' => [
                    'user_id' => 'cDdtNzl2UnZibUk9',
                    'marketing_preferences_code' => 'B2C_NEWSLETTER'
                ]
            ]
        )->assertStatus(204);
    }

    /**
     * Ensure PUT profile/marketing_preferences/unsubscribe sets the marketing preference to NEVER
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPUTSetsFrquencyToNever(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        $response = $this->putJson(
            $this->apiPrefix . '/profile/marketing_preferences/unsubscribe',
            [
                'data' => [
                    'user_id' => 'cDdtNzl2UnZibUk9',
                    'marketing_preferences_code' => 'B2C_NEWSLETTER'
                ]
            ]
        );

        UserMarketingPreference::join('marketing_preferences', 'marketing_preference_id', 'marketing_preferences.id')
            ->where('user_id', 2)
            ->where('marketing_preferences.code', 'B2C_NEWSLETTER')
            ->where('frequency', 'NEVER')
            ->firstOrFail();
    }
}
