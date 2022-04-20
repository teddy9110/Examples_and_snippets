<?php

namespace Rhf\Modules\Auth\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessUnauthorisedException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Auth\Requests\SignupRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    use ResetsPasswords;

    protected $guard = 'api';

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/password/success';

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            throw new FitnessUnauthorisedException('Unauthorised. Please verify your email and password are correct.');
        }

        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        if (!$user || !$user->active) {
            throw new FitnessUnauthorisedException('Unauthorised. Please contact Team RH Support.');
        }

        // In order to enable subscription capability within the iOS app, an optional 'subscriptions' header
        // param can be provided to respond with an additional field alongside the JWT token
        $subscribed = $user->isPaid();
        if (request()->input('subscriptions') == 'true') {
            return $this->respondWithSubscriptionAndToken($token, $subscribed);
        } elseif (!$subscribed) {
            throw new FitnessUnauthorisedException('Unauthorised. Please contact Team RH Support.');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Success route for password resets
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        return view('auth.passwords.success');
    }

    /**
     * @inheritdoc
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        try {
            return $this->respondWithToken($guard->refresh());
        } catch (TokenBlacklistedException | TokenInvalidException $e) {
            throw new FitnessUnauthorisedException('Unauthorised. Please sign back in.');
        } catch (JWTException $e) {
            throw new FitnessBadRequestException(
                'Error. Bad request.',
                ['token' => request()->header('Authorization')]
            );
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @param $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithSubscriptionAndToken($token, $subscription)
    {
        return response()->json([
            'subscribed' => $subscription,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Signup a user based on provided email and password.
     *
     * @param SignupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(SignupRequest $request)
    {
        try {
            if (request()->input('subscriptions') == 'true') {
                $user = User::where('email', '=', $request->get('email'))->inactive()->firstOrFail();
            } else {
                $user = User::where('email', '=', $request->get('email'))->paid()->inactive()->firstOrFail();
            }
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Unable to set password. This may be due to you having already set your password previously. '
                . 'Please contact RH Fitness Support if you think this is a mistake.'
            );
        }

        $user->activate();
        $user->updatePassword($request->get('password'));

        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            throw new FitnessUnauthorisedException('Unauthorised');
        }

        $subscribed = $user->isPaid();
        if (request()->input('subscriptions') == 'true') {
            return $this->respondWithSubscriptionAndToken($token, $subscribed);
        } elseif (!$subscribed) {
            throw new FitnessUnauthorisedException('Unauthorised. Please contact Team RH Support.');
        }

        return $this->respondWithToken($token);
    }
}
