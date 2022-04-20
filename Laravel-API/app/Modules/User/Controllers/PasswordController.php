<?php

namespace Rhf\Modules\User\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Rhf\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Rhf\Modules\User\Models\User;

/**
 * Class PasswordController
 *
 * @package \Rhf\Http\Controllers\User
 */
class PasswordController extends Controller
{
    use SendsPasswordResetEmails, ResetsPasswords {
        ResetsPasswords::credentials insteadof SendsPasswordResetEmails;
    }

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(null, 204);
    }

    /**
     * @inheritdoc
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(null, 400);
    }

    /**
     * @inheritdoc
     */
    public function sendResetResponse(Request $request, $response)
    {
        return view('auth.passwords.reset_success');
    }

    public function broker()
    {
        return Password::broker('users');
    }

    public function sendReset(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            $this->sendResetLinkEmail($request);
        }
        return response()->noContent();
    }
}
