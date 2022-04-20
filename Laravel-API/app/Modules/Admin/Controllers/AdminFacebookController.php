<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Response;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\System\Services\FacebookExtender as Facebook;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Services\FacebookContentService;

class AdminFacebookController extends Controller
{
    private $facebookContentService;

    public function __construct(Facebook $fb)
    {
        $this->middleware(function ($request, $next) use ($fb) {
            if (Auth::user()->token != null) {
                $this->facebookContentService = new FacebookContentService($fb);
            } else {
                return redirect()->to('/admin/content/facebook/login');
            }
            return $next($request);
        });
    }

    /**
     * Add the post to local content storage.
     *
     * @param Request $request
     * @param int Facebook ID
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, $id)
    {
        // Retrieve the single post from facebook
        $post = $this->facebookContentService->fromPageById($id);

        // Migrate it to local storage
        $this->facebookContentService->migrate();

        return response(200);
    }

    /**
     * Remove the post from local content storage.
     *
     * @param Request $request
     * @param int Facebook ID
     * @return Response
     */
    public function remove(Request $request, $id)
    {
        // Delete the matching post from the content table
        $content = Content::where('facebook_id', '=', $id)->first();
        if ($content) {
            $content->delete();
        }

        return response(200);
    }

    /**
     * Retrieve posts as JSON.
     *
     * @return Response
     * @throws Exception
     */
    public function index()
    {
        $posts = $this->facebookContentService->allPosts();

        return response()->json([
            'data' => $posts
        ]);
    }

    /**
     * Retrieve posts as JSON.
     *
     * @param Request $request
     * @param int video id
     * @return Response
     * @throws Exception
     */
    public function getVideoUrl(Request $request, $id)
    {
        $post = $this->facebookContentService->videoById($id);

        return response()->json([
            'data' => $post
        ]);
    }
}
