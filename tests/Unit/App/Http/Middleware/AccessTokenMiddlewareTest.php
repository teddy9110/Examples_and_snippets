<?php

namespace Tests\Unit\App\Http\Middleware;

use App\Http\Middleware\AccessTokenMiddleware;
use App\Exceptions\UnauthorizedException;
use App\Support\TSRGJWT;
use Illuminate\Http\Request;
use Tests\TestCase;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Mockery;

class AccessTokenMiddlewareTest extends TestCase
{
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
        $memberClaim['user_id'] = 1;
        $memberClaim['username'] = 'test';

        $expiredClaim = $guestClaim;
        $expiredClaim['iat'] = Carbon::now()->subHour()->timestamp;
        $expiredClaim['exp'] = Carbon::now()->subHour()->timestamp;

        $this->guestJWTToken = 'Bearer '.JWT::encode($guestClaim, config('jwt.secret'), config('jwt.algo'));
        $this->memberJWTToken = 'Bearer '.JWT::encode($memberClaim, config('jwt.secret'), config('jwt.algo'));
        $this->expiredJWTToken = 'Bearer '.JWT::encode($expiredClaim, config('jwt.secret'), config('jwt.algo'));

        $this->invalidJWTToken = 'Bearer '.JWT::encode($guestClaim, 'invalid', config('jwt.algo'));
    }

    /**
     * Check that the original request is passed through the middleware with no token (a guest token should be generated)
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testMiddlewarePassThroughWithNoToken(): void
    {
        $jwtTokenMock = Mockery::mock('alias:App\Support\TSRGJWT')->makePartial();
        $jwtTokenMock->shouldReceive('createGuestAccessToken')->once()->andReturn(new TSRGJWT());

        $request = new Request;

        $middleware = new AccessTokenMiddleware();

        $middleware->handle($request, function ($req) use ($request) {
            self::assertEquals($req, $request);
        });
    }

    /**
     * Check that the original request is passed through the middleware with a guest token
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testMiddlewarePassThroughWithGuestToken(): void
    {
        // Create a fake request
        $request = $this->createFakeRequestObject();
        $request->headers->set('Authorization', $this->guestJWTToken);

        $middleware = new AccessTokenMiddleware();

        $middleware->handle($request, function ($req) use ($request) {
            self::assertEquals($req, $request);
        });
    }

    /**
     * Check that the original request is passed through the middleware with a member token
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testMiddlewarePassThroughWithMemberToken(): void
    {
        // Create a fake request
        $request = $this->createFakeRequestObject();
        $request->headers->set('Authorization', $this->memberJWTToken);

        $middleware = new AccessTokenMiddleware();

        $middleware->handle($request, function ($req) use ($request) {
            self::assertEquals($req, $request);
        });
    }

    /**
     * Check that an exception is thrown if the token is expired
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testMiddlewareNoPassThroughWithExpiredToken(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Expired access token');

        // Create a fake request
        $request = $this->createFakeRequestObject();
        $request->headers->set('Authorization', $this->expiredJWTToken);

        $middleware = new AccessTokenMiddleware();

        $middleware->handle($request, static function ($req) {
            // Nothing - an exception should have been thrown
        });
    }

    /**
     * Check that an exception is thrown if the token is invalid
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testMiddlewareNoPassThroughWithInvalidToken(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Invalid access token');

        // Create a fake request
        $request = $this->createFakeRequestObject();
        $request->headers->set('Authorization', $this->invalidJWTToken);

        $middleware = new AccessTokenMiddleware();

        $middleware->handle($request, static function ($req) {
            // Nothing - an exception should have been thrown
        });
    }
}
