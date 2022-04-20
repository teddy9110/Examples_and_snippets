<?php

namespace Rhf\Modules\Video\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\Recipe\Services\RecipeImageFileService;
use Rhf\Modules\Video\Filters\VideoFilter;
use Rhf\Modules\Video\Models\Video;

class VideoService
{
    private $video;

    public function __construct(Video $videos)
    {
        $this->video = $videos;
    }

    public function getAll()
    {
        return $this->video->all();
    }

    public function getVideos(VideoFilter $filters, $pagination, $includeInactive = false)
    {
        $q = $this->video->with('tags');

        if (!$includeInactive) {
            $q = $q->where('active', 1);
        }

        return $q->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    public function getVideo($id)
    {
        return $this->video
            ->with('tags')
            ->findOrFail($id);
    }

    public function getVideosByDate(Carbon $date, $pagination)
    {
        return $this->video
            ->with('tags')
            ->where('active', 1)
            ->where('scheduled_date', $date)
            // TODO: Remove this when proper ordering is used
            ->orderBy('updated_at', 'desc')
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    public function createVideo($data, $thumbnail)
    {
        unset($data['thumbnail']);

        $video = $this->video->create($data);
        $thumbnail = $this->storeThumbnail($thumbnail, $video->id);

        $video->update([
            'thumbnail' => $thumbnail['path'] . '/' . $thumbnail['file_name']
        ]);
        return $video;
    }

    public function updateVideo($id, $data)
    {
        $video = $this->getVideo($id);
        $video->update($data);
        $video->save();
        return $video;
    }

    public function updateVideoThumbnail($id, $image)
    {
        $video = $this->getVideo($id);
        $deleteExistingImage = $this->deleteThumbnail($video);

        $thumbnail = $this->storeThumbnail($image, $video->id);
        $video->thumbnail = $thumbnail['path'] . '/' . $thumbnail['file_name'];
        $video->save();
        return $video;
    }

    public function deleteVideo($id)
    {
        $video = $this->getVideo($id);
        return $video->delete();
    }

    public function incrementOpenCount($id)
    {
        $video = $this->getVideo($id);
        $video->increment('open_count', 1);
        return $video;
    }

    public function incrementViewCount($id)
    {
        $video = $this->getVideo($id);
        $video->increment('view_count', 1);
        return $video;
    }

    public function hasNewVideos($date)
    {
        return $this->video->where('updated_at', '>', $date)->where('active', 1)->count();
    }

    public function storeThumbnail(UploadedFile $thumbnail, $id)
    {
        $fileService = new RecipeImageFileService();
        $imagePath = $fileService->createFromUpload(
            $thumbnail,
            'rhtv/' . $id,
            false
        );
        return $imagePath;
    }

    /**
     * @return mixed
     */
    public function getThumbnailImage($image)
    {
        return $this
            ->getStorageDisk()
            ->temporaryUrl(
                config('filesystems.disks.spaces.namespace') . '/' . $image,
                Carbon::now()->addMinutes(60)
            );
    }

    /**
     * @inheritdoc
     */
    public function getStorageDisk(): Filesystem
    {
        return Storage::disk(StorageLocations::SPACES);
    }

    /**
     * Delete images of user
     *
     * @param $id
     */
    private function deleteThumbnail(Video $video): void
    {
        $this->getStorageDisk()->delete([
            config('filesystems.disks.spaces.namespace') . '/' . $video->thumbnail,
        ]);
    }
}
