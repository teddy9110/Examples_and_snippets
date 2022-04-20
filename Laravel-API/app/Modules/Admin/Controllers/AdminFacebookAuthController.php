<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Rhf\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserRole;

class AdminFacebookAuthController extends Controller
{
    /**
     * @deprecated 1.12 - Laravel 8 Upgrade
     */

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function authRedirectToFacebookProvider()
    {
        return Socialite::driver('facebook')
            ->scopes(["groups_access_member_info, manage_pages, pages_show_list, publish_to_groups"])
            ->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return RedirectResponse
     */
    public function authHandleProviderFacebookCallback()
    {
        $auth_user = Socialite::driver('facebook')->user();
        $adminGodRoles = UserRole::whereIn('slug', ['admin', 'higher-admin', 'god'])->pluck('id')->toArray();

        User::whereIn('role_id', $adminGodRoles)
            ->update(['token' => $auth_user->token]);

        return redirect()->to('/admin'); // Redirect to a secure page
    }
}
