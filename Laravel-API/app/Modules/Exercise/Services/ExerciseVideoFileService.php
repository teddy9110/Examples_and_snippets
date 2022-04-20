<?php

namespace Rhf\Modules\Exercise\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\System\Contracts\FileServiceInterface;
use Rhf\Modules\System\Services\FileService;

class ExerciseVideoFileService extends FileService implements FileServiceInterface
{
    /*
     * Override getPublicUrl as the files are currently not uploaded to S3 via the FileService,
     * and instead uploaded manually with a filename set on them, so we need to set the correct path
     * based on the content of the model, the filepath. Content also does not have a 'public' model
     * field, which the FileService requires in the getPublicUrl method
     */
    public function getPublicUrl($model)
    {
        // Due to how exercise videos are bundled together, one part of the application
        // uses this as an array, another as a direct object, so handle both
        $content = is_array($model) ? $model['content_video'] : $model->content_video;

        return $this->getStorageDisk(false)
            ->temporaryUrl(
                config('filesystems.disks.spaces.namespace') . '/' . $content,
                Carbon::now()->addMinutes(60)
            );
    }

    /**
     * @inheritdoc
     */
    public function getStorageDisk($public): Filesystem
    {
        return Storage::disk(StorageLocations::SPACES);
    }
}
