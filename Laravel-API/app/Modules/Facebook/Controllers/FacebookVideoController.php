<?php

namespace Rhf\Modules\Facebook\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Facebook\Models\FacebookVideo;
use Rhf\Modules\Facebook\Resources\FacebookVideoResource;

class FacebookVideoController extends Controller
{
    /**
     * @deprecated 1.12 - Laravel 8 Upgrade
     */

    /**
     * Get paginated facebook videos
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));

        $facebookVideos = FacebookVideo::query()->orderBy('live', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return FacebookVideoResource::collection($facebookVideos);
    }
}
