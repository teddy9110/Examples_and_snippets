<?php

namespace Rhf\Modules\Workout\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\System\Contracts\FileServiceInterface;
use Rhf\Modules\System\Services\FileService;

class WorkoutFileService extends FileService implements FileServiceInterface
{
    /**
     * Set file attribute name.
     *
     * @return void
     */
    public function setFileAttribute($key)
    {
        $this->fileAttribute = $key;
    }

    public function getPublicUrl($model)
    {
        if (is_null($this->fileAttribute)) {
            return null;
        }

        $content = is_array($model) ? $model[$this->fileAttribute] : $model->{$this->fileAttribute};

        return $this->getStorageDisk(false)
            ->temporaryUrl(
                $this->getStorageNamespace() . $content,
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

    /**
     * @inheritdoc
     */
    public function createFromUpload(UploadedFile $file, $path, $public = true)
    {
        // Grab the extension from the file first to ensure we can save this onto the file name
        $extension = $file->extension();

        // Generate a UUID filename
        $uuid = Uuid::uuid4()->toString();

        // Append on the extension to use as a full filename
        $fileName = $uuid . "." . $extension;

        // Append the env onto the path
        $remotePath = $this->getStorageNamespace() . $path;

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

    /**
     * @inheritdoc
     */
    public function delete($model)
    {
        if (is_null($this->fileAttribute)) {
            return null;
        }

        return $this
            ->getStorageDisk(false)
            ->delete($this->getStorageNamespace() . $model->{$this->fileAttribute});
    }

    private function getStorageNamespace()
    {
        return config('filesystems.disks.spaces.namespace') . '/';
    }
}
