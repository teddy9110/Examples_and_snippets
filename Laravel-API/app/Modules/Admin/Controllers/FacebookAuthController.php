<?php

namespace Rhf\Modules\Admin\Controllers;

use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\System\Services\FacebookExtender as Facebook;
use Laravel\Socialite\Facades\Socialite;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserRole;

class FacebookAuthController extends Controller
{
    /**
     * @deprecated 1.12 - Laravel 8 Upgrade
     */

    private $appApi; // The facebook api class for the app
    private $userApi; // The facebook api class for the user
    private $pageApi; // The facebook api class for the page

    public function __construct(Facebook $fb)
    {
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authHandleProviderFacebookCallback()
    {
        $auth_user = Socialite::driver('facebook')->user();
        $adminGodRoles = UserRole::whereIn('slug', ['admin', 'higher-admin', 'god'])->pluck('id')->toArray();
        User::whereIn('role_id', $adminGodRoles)->update(['token' => $auth_user->token]);

        return redirect()->to('/admin/home'); // Redirect to a secure page
    }

    /**
     * Use the API to deauthorise test users so their permissions can be refreshed.
     *
     * @return \Illuminate\Http\Response
     */
    public function deauthoriseTestUsers(Request $request)
    {
        $response = $this->appApi->delete('/61100830/permissions')->getGraphEdge()->asArray();
    }
}
