<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileQualificationsController;

use App\Models\UserProfile;
use App\Models\UserProfileQualification;
use Carbon\Carbon;
use Faker\Factory;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\TSRGJWT;
use App\Support\Graph;
use Mockery;
use TSR\EventBridgeIngestion\Publisher;

class PutStageTest extends TestCase
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

        $jwtClass = new TSRGJWT(json_decode(json_encode(
            [
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
     * Ensure PUT /profile/{userId}/qualifications/{stage} returns 401 Unauthorised when guests try to access the API
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns401UnauthorisedWhenGuestTriesToAccess(): void
    {

        $this->withHeaders(['Authorization' => $this->guestJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/qualifications/current', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 2,
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure PUT /profile/{userId}/qualifications/{stage} returns 401 Unauthorised when guests try to access the API
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturnsInvalidParamiterException(): void
    {
    // Mock sending user updated event to eventbridge
    $publisherMock = Mockery::mock('overload:'.Publisher::class);
    $publisherMock->shouldReceive('publish')->once();

    UserProfile::factory()->count(1)->create(['user_id' => 2]);
    UserProfileQualification::factory()->create([
        'user_id' => 2,
        'qualification_id' => 2,
        'current' => true
    ]);

    $this->withHeaders(['Authorization' => $this->memberJWTToken])
        ->putJson($this->apiPrefix . '/profile/2/qualifications/current', [
            'data' => [
                'qualifications' => [
                    [
                        'qualification_id' => null,
                        'start_year' => null,
                        'end_year' => '2024'
                    ]
                ]
            ]
        ])->assertStatus(400);
    }

    /**
     * Ensure PUT /profile/{userId}/qualifications/{stage} returns 401 Unauthorised when access token has expired
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns401UnauthorisedWhenAccessTokenHasExpired(): void
    {
        $this->withHeaders(['Authorization' => $this->expiredJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/qualifications/current', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 2,
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Expired access token']]
            ]);
    }

    /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 401 Unauthorised when access token is invalid
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns401UnauthorisedWhenAccessTokenIsInvalid(): void
    {
        $this->withHeaders(['Authorization' => $this->invalidJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/qualifications/current', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 2,
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'Invalid access token']]
            ]);
    }

    /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 401 Unauthorised when user tries to access another user's profile
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns401UnauthorizedWhenUserTriesToAccessAnotherUsersProfile(): void
    {
        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/1/qualifications/current', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 2,
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(401)
            ->assertExactJson([
                'errors' => [['code' => 'UNAUTHORIZED', 'message' => 'You do not have permission to perform this action.']]
            ]);
    }

    /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 204 and the user's qualification is updated
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns204AndProfileIsUpdated(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileQualification::factory()->create([
            'user_id' => 2,
            'qualification_id' => 2,
            'current' => true
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/qualifications/current', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 2,
                            'start_year' => null,
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $qualification = UserProfile::where('user_id', 2)->with('qualifications')->first()->qualifications;
        self::assertCount(1, $qualification);
        $qualification = $qualification->first();
        self::assertEquals($qualification['user_id'], 2);
        self::assertEquals($qualification['qualification_id'], 2);
        self::assertEquals($qualification['end_year'], '2024');
    }

     /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 204 and the user's qualification is updated with future
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns204AndProfileIsUpdatedWhenFuture(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileQualification::factory()->create([
            'user_id' => 2,
            'qualification_id' => 2,
            'current' => true
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/qualifications/future', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 3,
                            'start_year' => '2024',
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $qualification = UserProfile::where('user_id', 2)->with('qualifications')->first()->qualifications;
        self::assertCount(2, $qualification);
        $qualification = $qualification[1];
        self::assertEquals($qualification['user_id'], 2);
        self::assertEquals($qualification['qualification_id'], 3);
        self::assertEquals($qualification['end_year'], '2024');
        self::assertEquals($qualification['start_year'], '2024');
    }

    /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 204 and the user's qualification is updated with future and postgrad
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns204AndProfileIsUpdatedWhenFutureAndPostgrad(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileQualification::factory()->create([
            'user_id' => 2,
            'qualification_id' => 2,
            'current' => true
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/qualifications/future', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 29,
                            'start_year' => '2024',
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $userProfile = UserProfile::where('user_id', 2)->with('qualifications')->first();
        self::assertEquals($userProfile->intended_postgraduate_start_year, '2024');
        $qualification = UserProfile::where('user_id', 2)->with('qualifications')->first()->qualifications;
        self::assertCount(2, $qualification);
        $qualification = $qualification[1];
        self::assertEquals($qualification['user_id'], 2);
        self::assertEquals($qualification['qualification_id'], 29);
        self::assertEquals($qualification['end_year'], '2024');
        self::assertEquals($qualification['start_year'], '2024');
    }

    /**
     * Ensure patch /profile/{userId}/qualifications/{stage} returns 204 and the user's qualification is updated with future and undergrad
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturns204AndProfileIsUpdatedWhenFutureAndUndergrad(): void
    {
        // Mock sending user updated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        UserProfile::factory()->count(1)->create(['user_id' => 2]);
        UserProfileQualification::factory()->create([
            'user_id' => 2,
            'qualification_id' => 2,
            'current' => true
        ]);

        $this->withHeaders(['Authorization' => $this->memberJWTToken])
            ->putJson($this->apiPrefix . '/profile/2/qualifications/future', [
                'data' => [
                    'qualifications' => [
                        [
                            'qualification_id' => 28,
                            'start_year' => '2024',
                            'end_year' => '2024'
                        ]
                    ]
                ]
            ])->assertStatus(204);

        // check the provided fields have been updated
        $userProfile = UserProfile::where('user_id', 2)->with('qualifications')->first();
        self::assertEquals($userProfile->intended_university_start_year, '2024');
        $qualification = UserProfile::where('user_id', 2)->with('qualifications')->first()->qualifications;
        self::assertCount(2, $qualification);
        $qualification = $qualification[1];
        self::assertEquals($qualification['user_id'], 2);
        self::assertEquals($qualification['qualification_id'], 28);
        self::assertEquals($qualification['end_year'], '2024');
        self::assertEquals($qualification['start_year'], '2024');
    }
}
