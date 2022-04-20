<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminFacebookVideoRequest;
use Rhf\Modules\Admin\Requests\AdminFacebookVideoThumbnailRequest;
use Rhf\Modules\Facebook\Models\FacebookVideo;
use Rhf\Modules\Facebook\Resources\FacebookVideoResource;
use Rhf\Modules\Facebook\Services\FacebookVideoService;

class AdminFacebookVideoController extends Controller
{
    private $facebookVideoService;

    public function __construct(FacebookVideoService $facebookVideoService)
    {
        $this->facebookVideoService = $facebookVideoService;
    }

    /**
     * Get paginated facebook videos
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'asc');
        $filterBy = $request->get('filter_by');
        $filterValue = $request->get('filter');

        $query = FacebookVideo::query()
            ->orderBy($orderBy, $orderDirection);

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        $facebookVideos = $query->paginate($perPage);
        return FacebookVideoResource::collection($facebookVideos);
    }

    /**
     * Show specific facebook video
     *
     * @param int $id
     * @return FacebookVideoResource
     */
    public function show(int $id)
    {
        return new FacebookVideoResource(FacebookVideo::findOrFail($id));
    }

    /**
     * Create facebook video
     *
     * @param AdminFacebookVideoRequest $request
     * @return FacebookVideoResource
     * @throws Exception
     */
    public function store(AdminFacebookVideoRequest $request)
    {
        $facebookVideo = $this->facebookVideoService->createFacebookVideo($request->all(), $request->file('thumbnail'));
        return new FacebookVideoResource($facebookVideo);
    }

    /**
     * Update facebook video
     *
     * @param int $id
     * @param AdminFacebookVideoRequest $request
     * @return FacebookVideoResource
     *
     * @throws Exception
     */
    public function update(int $id, AdminFacebookVideoRequest $request)
    {
        $facebookVideo = FacebookVideo::findOrFail($id);
        $this->facebookVideoService->setFacebookVideo($facebookVideo);

        $this->facebookVideoService->updateFacebookVideo($request->all(), $request->file('thumbnail'));
        return new FacebookVideoResource($facebookVideo);
    }

    /**
     * Delete facebook video
     *
     * @param int $id
     * @return ResponseFactory|Response
     */
    public function remove(int $id)
    {
        FacebookVideo::findOrFail($id)->delete();
        return response(null, 204);
    }

    /**
     * Update the given facebook video thumbnail
     *
     * @param AdminFacebookVideoThumbnailRequest $request
     * @param $id
     *
     * @return FacebookVideoResource
     * @throws Exception
     */
    public function updateThumbnail(AdminFacebookVideoThumbnailRequest $request, $id)
    {
        $facebookVideo = FacebookVideo::findOrFail($id);
        $this->facebookVideoService->setFacebookVideo($facebookVideo);

        $this->facebookVideoService->updateFacebookVideoThumbnail($request->file('thumbnail'));
        return new FacebookVideoResource($facebookVideo);
    }
}
