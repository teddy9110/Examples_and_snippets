<?php

namespace Rhf\Modules\Admin\Controllers;

use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\System\Services\FacebookExtender as Facebook;
use Laravel\Socialite\Facades\Socialite;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Services\FacebookContentService;

class FacebookContentController extends Controller
{
    /**
     * @deprecated 1.12 - Laravel 8 Upgrade
     */

    private $appApi; // The facebook api class for the app
    private $userApi; // The facebook api class for the user
    private $pageApi; // The facebook api class for the page

    public function __construct(Facebook $fb)
    {
        $this->middleware(function ($request, $next) use ($fb) {
            if (Auth::user()->token != null) {
                $fb->setDefaultAccessToken(
                    config('services.facebook.client_id') . '|' . config('services.facebook.client_secret')
                );
                $this->appApi = $fb;
                $this->userApi = (clone $fb);
                $this->userApi->setDefaultAccessToken(Auth::user()->token);
                $this->pageApi = (clone $this->userApi);
                $this->pageApi->authPageAccessToken();
            } else {
                return redirect()->to('/admin/content/facebook/login');
            }
            return $next($request);
        });
    }

    /**
     * Add the post to local content storage.
     *
     * @param int Facebook ID
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $id)
    {
        // Retrieve the single post from facebook
        $facebookContentService = new FacebookContentService($this->pageApi, $this->userApi, $this->appApi);
        $post = $facebookContentService->fromPageById($id);

        // Migrate it to local storage
        $facebookContentService->migrate();

        return response()->json([
            'status' => 'success',
        ]);
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
     * Show the item list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('facebookContent/index');
    }

    /**
     * Retrieve posts as JSON.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $facebookContentService = new FacebookContentService($this->pageApi, $this->userApi, $this->appApi);
        $posts = $facebookContentService->allPosts();

        return response()->json([
            'data' => $posts,
            'recordsTotal' => count($posts),
            'recordsFiltered' => count($posts),
        ]);
    }

    /**
     * Retrieve posts as JSON.
     *
     * @param (int) video id
     * @return \Illuminate\Http\Response
     */
    public function getVideoUrl(Request $request, $id)
    {
        $facebookContentService = new FacebookContentService($this->pageApi, $this->userApi, $this->appApi);
        $post = $facebookContentService->videoById($id);
        print_r($post);
    }

    /**
     * Remove the post from local content storage.
     *
     * @param int Facebook ID
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, $id)
    {
        // Delete the matching post from the content table
        $content = Content::where('facebook_id', '=', $id)->first();
        if ($content) {
            $content->delete();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Use the API to deauthorise test users so their permissions can be refreshed.
     *
     * @return \Illuminate\Http\Response
     */
    public function deauthoriseTestUsers(Request $request)
    {
        $response = $this->appApi->delete('/matt.wade2/permissions')->getGraphEdge()->asArray();
    }
}
