<?php

namespace Rhf\Modules\Video\Controllers;

use Carbon\Carbon;
use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Tags\Models\Tag;
use Rhf\Modules\Tags\Resources\TagsResource;
use Rhf\Modules\Video\Filters\VideoFilter;
use Rhf\Modules\Video\Models\UserVideoNotifications;
use Rhf\Modules\Video\Requests\VideoRequest;
use Rhf\Modules\Video\Resources\VideoResource;
use Rhf\Modules\Video\Services\VideoService;

class VideoController extends Controller
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
     * Return all videos, un-paginated
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(VideoRequest $request)
    {
        $filters = $request->validated();
        $filters['order']['sort_by'] = $request->input('sort_by', null);
        $filters['order']['sort_direction'] = $request->input('sort_direction', null);

        $pagination['page'] = $request->input('page');
        $pagination['per_page'] = $request->input('limit', 20);

        $videos = $this->videoService->getVideos(new VideoFilter($filters), $pagination);
        return VideoResource::collection($videos);
    }

    /**
     * Return single video
     * @param $id
     * @return VideoResource
     */
    public function show($id)
    {
        $this->videoService->incrementOpenCount($id);
        return new VideoResource($this->videoService->getVideo($id));
    }

    /**
     * Increment View count
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function watch($id)
    {
        $this->videoService->incrementViewCount($id);
        return response()->noContent();
    }

    /**
     * Check if any new videos have been created since specific date
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function new()
    {
        try {
            $user = auth('api')->user();
            $hasNotification = UserVideoNotifications::where('user_id', $user->id)->first();
            if ($hasNotification) {
                $newVideos = $this->videoService->hasNewVideos($hasNotification->notifications_read);
            } else {
                $newVideos = $this->videoService->hasNewVideos($user->created_at);
            }

            return response()->json(
                [
                    'new' => $newVideos > 0,
                    'count' => $newVideos
                ]
            );
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Sorry, there was a problem with your request');
        }
    }

    public function acknowledge()
    {
        try {
            $user = auth('api')->user();
            $updateNotification = UserVideoNotifications::where('user_id', $user->id)->first();
            if ($updateNotification) {
                $updateNotification->update(
                    [
                        'notifications_read' => now()
                    ]
                );
            } else {
                UserVideoNotifications::create([
                    'user_id' => $user->id,
                    'notifications_read' => now()
                ]);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Sorry, unable to complete this request');
        }
    }

    public function getTags()
    {
        $tags = Tag::where('type', 'video')->get();
        return TagsResource::collection($tags);
    }

    public function getByDate(VideoRequest $request)
    {
        $pagination['page'] = $request->input('page');
        $pagination['per_page'] = $request->input('limit', 20);

        $videos = $this->videoService->getVideosByDate(
            Carbon::parse($request->input('date', now()->format('Y-m-d'))),
            $pagination
        );
        return VideoResource::collection($videos);
    }
}
