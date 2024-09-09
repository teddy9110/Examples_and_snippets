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

class GetTest extends TestCase
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
     * Ensure GET /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->get($this->apiPrefix . '/profile/1/data_sharing_preferences', ['Authorization' => $this->guestJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->get($this->apiPrefix . '/profile/1/data_sharing_preferences', ['Authorization' => $this->expiredJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->get($this->apiPrefix . '/profile/1/data_sharing_preferences', ['Authorization' => $this->invalidJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->get($this->apiPrefix . '/profile/1/data_sharing_preferences', ['Authorization' => $this->memberJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns data sharing information in the correct structure
     *
     * @return void
     * 
     * @throws \JsonException
     */
    public function testGETReturnsCorrectStructure(): void
    {
        $response = $this->get($this->apiPrefix . '/profile/2/data_sharing_preferences', ['Authorization' => $this->memberJWTToken]);

        $preferences = $response['data']['data_sharing_preferences'];

        self::assertEquals(200, $response->status());
        self::assertTrue(array_key_exists('share_study_level', $preferences));
        self::assertTrue(array_key_exists('share_subjects_studying', $preferences));
        self::assertTrue(array_key_exists('share_university_subjects', $preferences));
        self::assertTrue(array_key_exists('share_university_choices', $preferences));
        self::assertTrue(array_key_exists('share_university_start_year', $preferences));
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns the correct content and defaults to false
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturnsCorrectContentAndDefaultsToFalse(): void
    {
        $response = $this->get($this->apiPrefix . '/profile/2/data_sharing_preferences', ['Authorization' => $this->memberJWTToken]);

        $preferences = $response['data']['data_sharing_preferences'];

        self::assertEquals(200, $response->status());
        self::assertFalse($preferences['share_study_level']);
        self::assertFalse($preferences['share_subjects_studying']);
        self::assertFalse($preferences['share_university_subjects']);
        self::assertFalse($preferences['share_university_choices']);
        self::assertFalse($preferences['share_university_start_year']);
    }

    /**
     * Ensure GET /profile/{userId}/data_sharing_preferences returns the correct content when user has set preferences
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturnsCorrectContentWhenUserHasSetPreferences(): void
    {
        UserProfileDataSharingPreference::factory()->count(1)->create([
            'user_id' => 2,
            'question_code' => 'study_level'
        ]);

        $response = $this->get($this->apiPrefix . '/profile/2/data_sharing_preferences', ['Authorization' => $this->memberJWTToken]);

        $preferences = $response['data']['data_sharing_preferences'];
        
        self::assertEquals(200, $response->status());
        self::assertTrue($preferences['share_study_level']);
        self::assertFalse($preferences['share_subjects_studying']);
        self::assertFalse($preferences['share_university_subjects']);
        self::assertFalse($preferences['share_university_choices']);
        self::assertFalse($preferences['share_university_start_year']);
    }
}
