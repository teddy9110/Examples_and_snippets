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

class DeleteTest extends TestCase
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
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/1/data_sharing_preferences/study_level')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/1/data_sharing_preferences/study_level')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/1/data_sharing_preferences/study_level')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/1/data_sharing_preferences/study_level')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns 400 if missing params
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturns400ForInvalidParams(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/2/data_sharing_preferences/not_a_real_code')
            ->assertStatus(400)
            ->assertExactJson([
                'errors' => [['code' => 'INVALID_PARAMETER', 'message' => 'Data sharing preference question code is invalid']]
            ]);
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns correct status code
     *
     * @return void
     * 
     * @throws \JsonException
     */
    public function testDELETEReturnsCorrectStatusCode(): void
    {
        UserProfileDataSharingPreference::factory()->count(1)->create([
            'user_id' => 2,
            'question_code' => 'study_level'
        ]);

        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/2/data_sharing_preferences/study_level');

        self::assertEquals(204, $response->status());
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns for user with no preferences
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturnsCorrectContentWhenUserHasNoPreferences(): void
    {
        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/2/data_sharing_preferences/study_level');

        // check the user still has no database entries
        $userSharing = UserProfileDataSharingPreference::where('user_id', 2);
        self::assertEquals(0, $userSharing->count());

        self::assertEquals(204, $response->status());
    }

    /**
     * Ensure DELETE /profile/{userId}/data_sharing_preferences/study_level returns for user with existing preferences
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEReturnsCorrectContentWhenUserHasPreferences(): void
    {
        UserProfileDataSharingPreference::factory()->count(1)->create([
            'user_id' => 2,
            'question_code' => 'study_level'
        ]);

        // check study_level has an entry in the database for the user
        $userSharing = UserProfileDataSharingPreference::where('user_id', 2);
        self::assertEquals(1, $userSharing->count());

        $response = $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->deleteJson($this->apiPrefix . '/profile/2/data_sharing_preferences/study_level');

        // check study_level does NOT have an entry in the database for the user
        $userSharing = UserProfileDataSharingPreference::where('user_id', 2);
        self::assertEquals(0, $userSharing->count());

        self::assertEquals(204, $response->status());
    }
}
