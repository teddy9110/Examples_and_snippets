<?php

namespace Rhf\Modules\Product\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Ramsey\Uuid\Uuid;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\System\Contracts\FileServiceInterface;
use Rhf\Modules\System\Services\FileService;

class ProductImageFileService extends FileService implements FileServiceInterface
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
        $this
            ->getStorageDisk($public)
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

    /* @inheritdoc */
    public function getPublicUrl($model)
    {
        if (is_null($model->image)) {
            return null;
        }

        return $this
            ->getStorageDisk(false)
            ->temporaryUrl(
                config('filesystems.disks.spaces.namespace') . '/' . $model->image,
                Carbon::now()->addMinutes(60)
            );
    }

    /* @inheritdoc */
    public function delete($model)
    {
        return $this
            ->getStorageDisk(false)
            ->delete(config('filesystems.disks.spaces.namespace') . '/' . $model->image);
    }

    /**
     * @inheritdoc
     */
    public function getStorageDisk($public): Filesystem
    {
        return Storage::disk(StorageLocations::SPACES);
    }
}
