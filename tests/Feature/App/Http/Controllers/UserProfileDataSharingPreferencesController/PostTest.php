<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileDataSharingPreferencesController;

use App\Models\UserProfileDataSharingPreference;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use TSR\EventBridgeIngestion\Publisher;
use Mockery;

class PostTest extends TestCase
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
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->postJson($this->apiPrefix . '/profile/1/data_sharing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->postJson($this->apiPrefix . '/profile/1/data_sharing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->postJson($this->apiPrefix . '/profile/1/data_sharing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson($this->apiPrefix . '/profile/1/data_sharing_preferences')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 400 if missing params
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns400ForInvalidParams(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson($this->apiPrefix . '/profile/2/data_sharing_preferences', ['data' => []])
            ->assertStatus(400)
            ->assertExactJson([
                'errors' => [['code' => 'INVALID_PARAMETER', 'message' => 'questionCode is required']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns 400 if missing params
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturns400IfMissingParams(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson(
                $this->apiPrefix . '/profile/2/data_sharing_preferences',
                [
                    'data' => [
                        'questionCode' => 'not_a_real_option'
                    ]
                ]
            )
            ->assertStatus(400)
            ->assertExactJson([
                'errors' => [['code' => 'INVALID_PARAMETER', 'message' => 'Data sharing preference question code is invalid']]
            ]);
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns correct status code
     *
     * @return void
     * 
     * @throws \JsonException
     */
    public function testPOSTReturnsCorrectStatusCode(): void
    {
        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson(
                $this->apiPrefix . '/profile/2/data_sharing_preferences',
                [
                    'data' => [
                        'questionCode' => 'study_level'
                    ]
                ]
            );

        self::assertEquals(204, $response->status());
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns for user with no preferences
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturnsCorrectContentWhenUserHasNoPreferences(): void
    {
        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson(
                $this->apiPrefix . '/profile/2/data_sharing_preferences',
                [
                    'data' => [
                        'questionCode' => 'study_level'
                    ]
                ]
            );

        $userSharing = UserProfileDataSharingPreference::where('user_id', 2)->first();

        self::assertEquals('study_level', $userSharing->question_code);
        self::assertEquals(204, $response->status());
    }

    /**
     * Ensure POST /profile/{userId}/data_sharing_preferences returns for user with existing preferences
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTReturnsCorrectContentWhenUserHasPreferences(): void
    {
        UserProfileDataSharingPreference::factory()->count(1)->create([
            'user_id' => 2,
            'question_code' => 'study_level'
        ]);

        $userSharing = UserProfileDataSharingPreference::where('user_id', 2)->first();
        $initialTime = $userSharing->updated_at;

        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->postJson(
                $this->apiPrefix . '/profile/2/data_sharing_preferences',
                [
                    'data' => [
                        'questionCode' => 'study_level'
                    ]
                ]
            );

        $userSharing = UserProfileDataSharingPreference::where('user_id', 2);
        $updatedTime = $userSharing->first()->updated_at;

        // check study_level only has one entry in the database for the user
        self::assertEquals(1, $userSharing->count());

        // check the updated_at field has been updated
        self::assertNotEquals($initialTime, $updatedTime);
        self::assertTrue($updatedTime > $initialTime);

        self::assertEquals(204, $response->status());
    }
}
