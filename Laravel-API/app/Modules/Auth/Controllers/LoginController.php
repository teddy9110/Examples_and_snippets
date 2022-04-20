<?php

namespace Rhf\Modules\Auth\Controllers;

use Rhf\Exceptions\FitnessUnauthorisedException;
use Rhf\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login user to token.
     *
     * @return void
     */
    public function login(Guard $auth, Request $request)
    {
        // get some credentials
        $credentials = $request->only(['email', 'password']);

        if ($auth->attempt($credentials)) {
            return $token = $auth->issue();
        }

        throw new FitnessUnauthorisedException('Unauthorised');
    }
}
