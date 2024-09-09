<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileInternationalController;

use App\Models\UserProfile;
use App\Models\UserProfileInternationalApplication;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\TSRGJWT;
use App\Support\UserProfileInternationalHelper;
use App\Support\Graph;
use Mockery;
use TSR\EventBridgeIngestion\Publisher;

class PatchTest extends TestCase
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
     * Ensure PATCH /profile/{userId}/international returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->patchJson($this->apiPrefix . '/profile/1/international')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId} returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->patchJson($this->apiPrefix . '/profile/1/international')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->patchJson($this->apiPrefix . '/profile/1/international')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 401 Unauthorised when user tries to access another user's international profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/1/international')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns204AndProfileIsUpdated(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [
                    "country_of_nationality" => 'test country 2',
                    "is_application_started" => true,
                    "international_agent" => 'test'
                ],
            ]])->assertStatus(204);

        // check the provided fields have been updated
        $profile = UserProfile::find(2);
        self::assertEquals($profile->internationalApplication['user_id'], 2);
        self::assertEquals($profile->internationalApplication['country_of_nationality'], "test country 2");
        self::assertEquals($profile->internationalApplication['is_application_started'], true);
        self::assertEquals($profile->internationalApplication['international_agent'], 'test');
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data making sure the missing data is not included
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns204AndProfileIsUpdatedWithCountryMissing(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [
                    "is_application_started" => true,
                    "international_agent" => 'test'
                ],
            ]])->assertStatus(204);

        // check the provided fields have been updated
        $profile = UserProfile::find(2);

        self::assertEquals($profile->internationalApplication['user_id'], 2);
        self::assertEquals($profile->internationalApplication['country_of_nationality'], null);
        self::assertEquals($profile->internationalApplication['is_application_started'], true);
        self::assertEquals($profile->internationalApplication['international_agent'], 'test');
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data making sure the missing data is not included
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns204AndProfileIsUpdatedWithOnlyCountryData(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [
                    "country_of_nationality" => 'country 1',
                ],
            ]])->assertStatus(204);

        // check the provided fields have been updated
        $profile = UserProfile::find(2);

        self::assertEquals($profile->internationalApplication['user_id'], 2);
        self::assertEquals($profile->internationalApplication['country_of_nationality'], 'country 1');
        self::assertEquals($profile->internationalApplication['is_application_started'], null);
        self::assertEquals($profile->internationalApplication['international_agent'], null);
    }


    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHDoesNotUpdateWithInvalidData(): void
    {
        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [
                    "is_application_started" => "test",
                    "international_agent" => 1
                ],
            ]])->assertStatus(400)
            ->assertExactJson([
                'errors' => [['code' => 'VALIDATION_ERROR', 'message' => 'Body does not match schema for content-type "application/json" for Request [patch /profiles/v1/profile/{userId}/international]']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHUpdatesCorrectlyWhenRowAllreadyExists(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        UserProfileInternationalApplication::factory()->count(1)->create([
            'user_id' => 2,
            'country_of_nationality' => 'country 2',
            'is_application_started' => true,
            'international_agent' => null,
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [
                    "is_application_started" => false,
                    "international_agent" => 'test'
                ],
            ]])->assertStatus(204);
        $profile = UserProfile::find(2);

        self::assertEquals($profile->internationalApplication['user_id'], 2);
        self::assertEquals($profile->internationalApplication['country_of_nationality'], 'country 2');
        self::assertEquals($profile->internationalApplication['is_application_started'], false);
        self::assertEquals($profile->internationalApplication['international_agent'], 'test');
    }
    /**
     * Ensure PATCH /profile/{userId}/international returns 204 and the user's profile is updated with international data
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHRejectsIfNoDataIsPassedIn(): void
    {
        UserProfile::factory()->count(1)->create([
            'user_id' => 2,
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'mobile' => '123456789',
            'post_code' => 'ABC 123',
            'country' => 'test_country',
            'gender' => 'gender 1,gender 2',
            'year_group' => 12,
            'intended_university_start_year' => '2024',
            'career_phase' => 'career phase 1,career phase 2'
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix . '/profile/2/international', ['data' => [
                'international_questions' => [],
            ]])->assertStatus(400)
            ->assertExactJson([
                'errors' => [['code' => 'INVALID_PARAMETER', 'message' => 'Invalid order by parameter values']]
            ]);
    }

}
