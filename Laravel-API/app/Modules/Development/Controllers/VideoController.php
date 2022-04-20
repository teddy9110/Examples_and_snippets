<?php

namespace Rhf\Modules\Development\Controllers;

use Carbon\Carbon;
use Exception;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Development\Requests\VideoCreationRequest;
use Rhf\Modules\Development\Resources\VideoResource;
use Rhf\Modules\Video\Services\VideoService;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function createVideo(VideoCreationRequest $request)
    {
        $thumbnail = UploadedFile::fake()->image(Str::random('22') . '.jpg', 1024, 1024)->size(900);
        $requestedVideo = $request->validated();

        if ($request->json('scheduled_date') == null) {
            $requestedVideo['scheduled_date'] = Carbon::now()->startOfDay()->addDays(rand(1, 365))->format('Y-m-d');
        }
        if ($request->json('scheduled_time') == null) {
            $requestedVideo['scheduled_time'] = Carbon::now()->startOfDay()->addHours(rand(0, 24))->format('h:m:i');
        }

        $video = $this->videoService->createVideo(
            $requestedVideo,
            $thumbnail
        );
        $video->tags()->toggle($request->input('tags'));
        return new VideoResource($video);
    }
}
