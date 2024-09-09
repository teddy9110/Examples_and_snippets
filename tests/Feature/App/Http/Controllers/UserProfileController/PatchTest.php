<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileController;

use App\Models\UserProfile;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\TSRGJWT;
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

        $jwtClass = new TSRGJWT(json_decode(json_encode([
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
     * Ensure PATCH /profile/{userId} returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {
        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->patchJson($this->apiPrefix.'/profile/1')
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
            ->patchJson($this->apiPrefix.'/profile/1')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId} returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->patchJson($this->apiPrefix.'/profile/1')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId} returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix.'/profile/1')
            ->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PATCH /profile/{userId} returns 204 and the user's profile is updated
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
            ->patchJson($this->apiPrefix.'/profile/2', ['data' => [
                'first_name' => 'updated_first_name',
                'last_name' => 'updated_last_name',
                'mobile' => '987654321',
                'post_code' => 'XYZ 987',
                'country' => 'updated_country'
            ]])->assertStatus(204);

        // check the provided fields have been updated
        $profile = UserProfile::find(2);
        // should find 2 audits here  as the profile was created and updated
        self::assertCount(2, $profile->audits);
        self::assertEquals(2, $profile->audits->first()->user_id);
        self::assertEquals($profile['first_name'], 'updated_first_name');
        self::assertEquals($profile['last_name'], 'updated_last_name');
        self::assertEquals($profile['mobile'], '987654321');
        self::assertEquals($profile['post_code'], 'XYZ 987');
        self::assertEquals($profile['country'], 'updated_country');

        // check the other fields have not been updated
        self::assertEquals($profile['user_id'], 2);
        self::assertEquals($profile['gender'], 'gender 1,gender 2');
        self::assertEquals($profile['year_group'], 12);
        self::assertEquals($profile['intended_university_start_year'], '2024');
        self::assertEquals($profile['career_phase'], 'career phase 1,career phase 2');
    }

    /**
     * Ensure PATCH /profile/{userId} creates a new user profile if one doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHReturnsCorrectContent(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        // check the profile does not exist
        $profile = UserProfile::find(2);
        self::assertNull($profile);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->patchJson($this->apiPrefix.'/profile/2', ['data' => [
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'mobile' => '07777777777',
                'post_code' => 'ABC 123',
                'country' => 'UK'
            ]])->assertStatus(204);

        // check the profile has been added to the database
        $profile = UserProfile::find(2);
        self::assertCount(1, $profile->audits);
        self::assertEquals(2, $profile->audits->first()->user_id);
        self::assertEquals($profile['user_id'], 2);
        self::assertEquals($profile['first_name'], 'first_name');
        self::assertEquals($profile['last_name'], 'last_name');
        self::assertEquals($profile['mobile'], '07777777777');
        self::assertEquals($profile['post_code'], 'ABC 123');
        self::assertEquals($profile['country'], 'UK');
        self::assertNull($profile['gender']);
        self::assertNull($profile['year_group']);
        self::assertNull($profile['intended_university_start_year']);
        self::assertNull($profile['career_phase']);
    }
}
