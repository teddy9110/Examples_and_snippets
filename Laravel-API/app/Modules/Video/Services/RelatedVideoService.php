<?php

namespace Rhf\Modules\Video\Services;

use Illuminate\Http\UploadedFile;
use Rhf\Modules\Video\Models\RelatedVideo;

class RelatedVideoService
{
    /**
     * @var RelatedVideoImageService
     */
    private $relatedVideoImageService;

    public function __construct(RelatedVideoImageService $relatedVideoImageService)
    {
        $this->relatedVideoImageService = $relatedVideoImageService;
    }

    public function paginate(array $pagination)
    {
        return RelatedVideo::paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    public function getVideo($id)
    {
        return RelatedVideo::findOrFail($id);
    }

    public function createVideo(array $data, UploadedFile $image = null, $workoutId = null)
    {
        unset($data['thumbnail']);
        $video = isset($data['id']) ?
            $this->updateVideo($data['id'], $data) :
            RelatedVideo::create($data);

        if (!is_null($image)) {
            $this->relatedVideoImageService->deleteImage($video);
            $this->addImageToRelatedVideo($video, $image);
        }
        $video->workouts()->sync($workoutId);
        return $video;
    }

    public function updateVideo($id, $data)
    {
        $video = $this->getVideo($id);
        $video->update([
            'title' => $data['title'],
            'url' => $data['url'],
        ]);
        return $video;
    }

    private function addImageToRelatedVideo(RelatedVideo $video, UploadedFile $image): RelatedVideo
    {
        $updatedImage = $this->relatedVideoImageService->storeImage($image, $video->id);
        $video->update([
           'thumbnail' => $updatedImage['path'] . '/' . $updatedImage['file_name']
        ]);
        return $video;
    }

    public function deleteVideoImages(array $ids)
    {
        foreach ($ids as $id) {
            $this->relatedVideoImageService->deleteImage($this->getVideo($id));
        }
    }
}
