<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileController;

use App\Models\UserProfile;
use App\Models\UserProfileInternationalApplication;
use App\Models\UserProfileLearningProvider;
use App\Models\UserProfileQualification;
use App\Models\UserProfileSubject;
use App\Models\UserProfileTopicsOfInterest;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\TSRGJWT;


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

        config()->set('audit.console', true);

        $jwtClass = new TSRGJWT(json_decode(json_encode(
            [
                'user_id' => 2,
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

        $this->guestJWTToken = 'Bearer ' . JWT::encode($guestClaim, config('jwt.secret'), config('jwt.algo'));
        $this->memberJWTToken = 'Bearer ' . JWT::encode($memberClaim, config('jwt.secret'), config('jwt.algo'));
        $this->expiredJWTToken = 'Bearer ' . JWT::encode($expiredClaim, config('jwt.secret'), config('jwt.algo'));

        $this->invalidJWTToken = 'Bearer ' . JWT::encode($guestClaim, 'invalid', config('jwt.algo'));
    }

    /**
     * Ensure GET /profile/{userId} returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->get($this->apiPrefix . '/profile/1', ['Authorization' => $this->guestJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to access this data.']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId} returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->get($this->apiPrefix . '/profile/1', ['Authorization' => $this->expiredJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId} returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->get($this->apiPrefix . '/profile/1', ['Authorization' => $this->invalidJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId} returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->get($this->apiPrefix . '/profile/1', ['Authorization' => $this->memberJWTToken])
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to access this data.']]
            ]);
    }

    /**
     * Ensure GET /profile/{userId} returns 200 if no token, but correct internal key is provided
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns200WhenCorrectInternalKeyProvided(): void
    {
        $this->get($this->apiPrefix . '/profile/1', ['x-internal-key' => 'test_internal_key'])
            ->assertStatus(200);
    }

    /**
     * Ensure GET /profile/{userId} returns 200 and an empty profile if the profile doesn't already exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturns200AndEmptyProfileIfProfileDoesNotAlreadyExist(): void
    {
        $response = $this->get($this->apiPrefix . '/profile/2', ['Authorization' => $this->memberJWTToken]);
        self::assertEquals(200, $response->status());

        $profile = $response['data']['user_profile'];

        self::assertEquals($profile['user_id'], 2);
        self::assertEquals($profile['first_name'], '');
        self::assertEquals($profile['last_name'], '');
        self::assertEquals($profile['mobile'], '');
        self::assertEquals($profile['post_code'], '');
        self::assertEquals($profile['country'], '');
        self::assertEquals($profile['gender'], '');
        self::assertEquals($profile['year_group'], null);
        self::assertEquals($profile['intended_university_start_year'], null);
        self::assertEquals($profile['career_phase'], '');

        self::assertEquals($profile['qualifications'], []);
        self::assertEquals($profile['current_subjects'], []);
        self::assertEquals($profile['future_subjects'], []);
        self::assertEquals($profile['current_learning_providers'], []);
        self::assertEquals($profile['future_learning_providers'], []);

        self::assertEquals($profile['questions_answered'], 0);
        self::assertNull($profile['email_opted_out_at']);
        self::assertNull($profile['sms_opted_out_at']);
    }

    /**
     * Ensure GET /profile/{userId} returns profile information in the correct structure
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturnsCorrectStructure(): void
    {
        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileLearningProvider::factory()->count(1)->create(['user_id' => 2]);
        UserProfileQualification::factory()->count(1)->create(['user_id' => 2]);
        UserProfileSubject::factory()->count(1)->create(['user_id' => 2]);
        UserProfileTopicsOfInterest::factory()->count(1)->create(['user_id' => 2]);

        UserProfileInternationalApplication::factory()->count(1)->create(
            [
                'id' => 1,
                'user_id' => 2,
                'country_of_nationality' => 'test country',
                'is_application_started' => 1,
                'international_agent' => 'test'
            ],
        );

        $response = $this->get($this->apiPrefix . '/profile/2', ['Authorization' => $this->memberJWTToken]);

        self::assertEquals(200, $response->status());

        $profile = $response['data']['user_profile'];

        self::assertTrue(array_key_exists('user_id', $profile));
        self::assertTrue(array_key_exists('first_name', $profile));
        self::assertTrue(array_key_exists('last_name', $profile));
        self::assertTrue(array_key_exists('mobile', $profile));
        self::assertTrue(array_key_exists('post_code', $profile));
        self::assertTrue(array_key_exists('country', $profile));
        self::assertTrue(array_key_exists('gender', $profile));
        self::assertTrue(array_key_exists('year_group', $profile));
        self::assertTrue(array_key_exists('intended_university_start_year', $profile));
        self::assertTrue(array_key_exists('career_phase', $profile));
        self::assertTrue(array_key_exists('international_application', $profile));

        self::assertTrue(array_key_exists('qualifications', $profile));
        self::assertTrue(array_key_exists('current_subjects', $profile));
        self::assertTrue(array_key_exists('future_subjects', $profile));
        self::assertTrue(array_key_exists('current_learning_providers', $profile));
        self::assertTrue(array_key_exists('future_learning_providers', $profile));
        self::assertTrue(array_key_exists('questions_answered', $profile));
        self::assertTrue(array_key_exists('topics_of_interest', $profile));
        self::assertTrue(array_key_exists('email_opted_out_at', $profile));
        self::assertTrue(array_key_exists('sms_opted_out_at', $profile));
    }

    /**
     * Ensure GET /profile/{userId} returns the correct content
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETReturnsCorrectContent(): void
    {
        // Current academic year is 2024
        Carbon::setTestNow(Carbon::create(2024, 01, 01, 00, 00, 00));

        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1'
        ]);

        UserProfileLearningProvider::factory()->count(3)->sequence(
            ['id' => 1, 'user_id' => 2, 'learning_provider_id' => 2, 'previous' => 1, 'current' => 0, 'future' => 0],
            ['id' => 2, 'user_id' => 2, 'learning_provider_id' => 3, 'previous' => 0, 'current' => 1, 'future' => 0],
            ['id' => 3, 'user_id' => 2, 'learning_provider_id' => 4, 'previous' => 0, 'current' => 0, 'future' => 1]
        )->create();

        UserProfileQualification::factory()->count(4)->sequence(
            ['id' => 1, 'user_id' => 2, 'qualification_id' => 1, 'end_year' => 2025],
            ['id' => 2, 'user_id' => 2, 'qualification_id' => 2, 'end_year' => 2024],
            ['id' => 3, 'user_id' => 2, 'qualification_id' => 3, 'end_year' => 2023], // Past qualification, shouldn't return
            ['id' => 4, 'user_id' => 2, 'qualification_id' => 4, 'end_year' => null],
        )->create();

        UserProfileInternationalApplication::factory()->count(1)->create(
            [
                'id' => 1,
                'user_id' => 2,
                'country_of_nationality' => 'test country',
                'is_application_started' => 1,
                'international_agent' => 'test'
            ]
        );

        UserProfileSubject::factory()->count(3)->sequence(
            ['id' => 1, 'user_id' => 2, 'subject_id' => 5, 'predicted_grade' => 'A', 'previous' => 1, 'current' => 0, 'future' => 0],
            ['id' => 2, 'user_id' => 2, 'subject_id' => 6, 'predicted_grade' => 'B', 'previous' => 0, 'current' => 1, 'future' => 0],
            ['id' => 3, 'user_id' => 2, 'subject_id' => 7, 'predicted_grade' => 'C', 'previous' => 0, 'current' => 0, 'future' => 1]
        )->create();


        UserProfileTopicsOfInterest::factory()->count(1)->create(['user_id' => 2, 'topic_code' => 'abc', 'interested' => 1]);

        $response = $this->get($this->apiPrefix . '/profile/2', ['Authorization' => $this->memberJWTToken]);

        self::assertEquals(200, $response->status());

        $profile = $response['data']['user_profile'];

        self::assertCount(1, UserProfileQualification::where('user_id', 2)->first()->audits);
        self::assertEquals(2, UserProfileQualification::where('user_id', 2)->first()->audits->first()->user_id);
        self::assertEquals($profile['user_id'], 2);
        self::assertEquals($profile['first_name'], 'test_first_name');
        self::assertEquals($profile['last_name'], 'test_last_name');
        self::assertEquals($profile['mobile'], '123456789');
        self::assertEquals($profile['post_code'], 'ABC 123');
        self::assertEquals($profile['country'], 'test_country');
        self::assertEquals($profile['gender'], 'gender 1');
        self::assertEquals($profile['year_group'], 12);
        self::assertEquals($profile['intended_university_start_year'], '2024');
        self::assertEquals($profile['career_phase'], 'career phase 1');

        self::assertEquals($profile['qualifications'], [1, 2, 4]);

        self::assertEquals($profile['current_subjects'], [6]);
        self::assertEquals($profile['future_subjects'], [7]);

        self::assertEquals($profile['current_learning_providers'], [3]);
        self::assertEquals($profile['future_learning_providers'], [4]);
        self::assertEquals($profile['international_application']['country_of_nationality'], 'test country');
        self::assertEquals($profile['international_application']['is_application_started'], 1);
        self::assertEquals($profile['international_application']['international_agent'], 'test');
        self::assertEquals($profile['questions_answered'], 9);
        self::assertCount(1, $profile['topics_of_interest']);
        self::assertEquals($profile['topics_of_interest'][0]['code'], 'abc');
        self::assertEquals($profile['topics_of_interest'][0]['interested'], true);
        self::assertNull($profile['email_opted_out_at']);
        self::assertNull($profile['sms_opted_out_at']);
        //TODO : add in tests for end year structure 
    }
}
