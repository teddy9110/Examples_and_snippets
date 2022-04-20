<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminRHVideoRequest;
use Rhf\Modules\Admin\Resources\AdminVideoResource;
use Rhf\Modules\Tags\Models\Tag;
use Rhf\Modules\Tags\Resources\TagsResource;
use Rhf\Modules\Video\Filters\VideoFilter;
use Rhf\Modules\Video\Services\VideoService;

class AdminVideoController extends Controller
{
    /**
     * @var VideoService
     */
    private $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Return all videos
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws Exception
     */
    public function getVideos(Request $request)
    {
        try {
            $filters = $request->all();
            $filters['order']['sort_by'] = $request->input('sort_by', null);
            $pagination['page'] = intval($request->input('page', 1));
            $pagination['per_page'] = $request->input('limit', 20);
            $videos = $this->videoService->getVideos(new VideoFilter($filters), $pagination, true);
            return AdminVideoResource::collection($videos);
        } catch (Exception $e) {
            throw new Exception('Sorry, unable to complete this request', $e->getCode());
        }
    }

    /**
     * Return single video
     *
     * @param $id
     * @return AdminVideoResource
     * @throws Exception
     */
    public function getVideo($id)
    {
        try {
            return new AdminVideoResource($this->videoService->getVideo($id));
        } catch (Exception $e) {
            throw new Exception('Sorry, unable to complete this request', $e->getCode());
        }
    }

    /**
     * Submit a video
     *
     * @param AdminRHVideoRequest $request
     */
    public function submitVideo(AdminRHVideoRequest $request)
    {
        try {
            $video = $this->videoService->createVideo(
                $request->validated(),
                $request->file('thumbnail')
            );
            $video->tags()->toggle($request->input('tags'));
            return new AdminVideoResource($video);
        } catch (Exception $e) {
            throw new Exception('Sorry, unable to complete request', $e->getCode());
        }
    }

    /**
     * Edit a video - Incomplete due to time restrictions
     * Will come back to it and full implement
     *
     * @param AdminRHVideoRequest $request
     * @param $id
     */
    public function editVideo(AdminRHVideoRequest $request, $id)
    {
        try {
            $video = $this->videoService->updateVideo($id, $request->validated());
            $video->tags()->toggle($request->input('tags'));
            return response()->json([
                'message' => 'Video Successfully updated'
            ], 200);
        } catch (Exception $e) {
            throw new Exception('Sorry, unable to update video', $e->getCode());
        }
    }

    public function editThumbnail(Request $request, $id)
    {
        try {
            $image = $request->file('thumbnail');
            $video = $this->videoService->updateVideoThumbnail($id, $image);
            return response()->json([
                'message' => 'Video Thumbnail successfully updated'
            ], 200);
        } catch (Exception $e) {
            throw new Exception('Sorry, unable to update thumbnail', $e->getCode());
        }
    }

    public function tags()
    {
        return TagsResource::collection(Tag::where('type', 'video')->get());
    }

    /**
     * Delete a video
     *
     * @param $id
     */
    public function deleteVideo($id)
    {
        $this->videoService->deleteVideo($id);
        return response()->noContent();
    }
}
