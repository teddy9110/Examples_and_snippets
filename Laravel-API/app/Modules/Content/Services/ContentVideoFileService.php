<?php

namespace Rhf\Modules\Content\Services;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\System\Contracts\FileServiceInterface;
use Rhf\Modules\System\Services\FileService;

class ContentVideoFileService extends FileService implements FileServiceInterface
{
    /* @inheritdoc */
    public function createFromUpload(UploadedFile $file, $path, $public = true)
    {
        // Grab the extension from the file first to ensure we can save this onto the file name
        $extension = $file->extension();

        // Generate a UUID filename
        $uuid = Uuid::uuid4()->toString();

        // Append on the extension to use as a full filename
        $fileName = $uuid . "." . $extension;

        // Append the env onto the path
        $remotePath = config('filesystems.disks.spaces.namespace') . '/' . $path;

        // Store the file
        $this->getStorageDisk($public)
            ->putFileAs($remotePath, $file, $fileName, [
                'Content-Type' => $file->getMimeType(),
                'public' => $public ? 'public' : null
            ]);

        // Return the storage details
        return [
            'uuid' => $uuid,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'public' => $public,
            'extension' => $extension
        ];
    }

    /*
     * Override getPublicUrl as the files are currently not uploaded to S3 via the FileService,
     * and instead uploaded manually with a filename set on them, so we need to set the correct path
     * based on the content of the model, the filepath. Content also does not have a 'public' model
     * field, which the FileService requires in the getPublicUrl method
     */
    public function getPublicUrl($model)
    {
        if (is_null($model->content)) {
            return null;
        }

        // Temporarily point life plan videos to AWS S3 buckets because DigitalOcean Spaces is a slow boi
        if ($model->category->slug == "life-plan") {
            return Storage::disk(StorageLocations::S3_PRIVATE)
                ->temporaryUrl(
                    $model->content,
                    Carbon::now()->addMinutes(60)
                );
        }

        return $this->getStorageDisk(false)->temporaryUrl(
            config('filesystems.disks.spaces.namespace') . '/' . $model->content,
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
