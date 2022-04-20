<?php

namespace Rhf\Modules\System\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\System\Contracts\FileServiceInterface;

abstract class FileService implements FileServiceInterface
{
    /**
     * Given an uploaded file, will store the file on S3, returning
     * the stored details.
     *
     * @param UploadedFile $file
     * @param $path
     * @param bool $public
     * @return array
     * @throws \Exception
     */
    public function createFromUpload(UploadedFile $file, $path, $public = true)
    {
        // Generate a UUID if one hasn't been passed in
        $uuid = Uuid::uuid4();

        // Store the file
        $this->getStorageDisk($public)
            ->putFileAs($path, $file, $uuid, $public ? 'public' : null);

        // Return the storage details
        return [
            'uuid' => $uuid,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'public' => $public
        ];
    }

    /**
     * Given a string, will store the file on S3, returning
     * the stored details.
     *
     * @param string $file
     * @param string $name
     * @param $path
     * @param bool $public
     * @return array
     * @throws \Exception
     */
    public function createFromString(string $file, string $name, $path, $public = true)
    {
        // We need the UUID before the DB generates one for us
        $uuid = Uuid::uuid4();

        // Store the file
        $this->getStorageDisk($public)
            ->put($path . $uuid, $file);
        $this->getStorageDisk($public)
            ->setVisibility($path . $uuid, $public ? 'public' : null);

        // Return the storage details
        return [
            'uuid' => $uuid,
            'original_name' => $name,
            'path' => $path,
            'public' => $public
        ];
    }

    /**
     * Given a model, deletes the file on S3 (not the model itself).
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function delete($model)
    {
        return $this->getStorageDisk($model->public)
            ->delete($model->path . $model->uuid);
    }


    /**
     * Given a model, generates a download response from s3.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function getProxyDownload($model)
    {
        $storage = $this->getStorageDisk($model->public);
        $path = $model->path . $model->uuid;
        return $storage->download($path, $model->original_name, [
            'Content-Type' => $storage->mimeType($path),
        ]);
    }

    /**
     * Given a model, returns the public (or privately signed) url.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function getPublicUrl($model)
    {
        $path = $model->path . $model->uuid;

        if ($model->public) {
            return $this->getStorageDisk($model->public)
                ->url($path);
        } else {
            return $this->getStorageDisk($model->public)
                ->temporaryUrl(
                    $path,
                    Carbon::now()->addMinutes(60)
                );
        }
    }

    protected function proxyUrl()
    {
        return 'files';
    }

    public function generatePath($id)
    {
        return $id . '/files/';
    }

    /**
     * Given a model, provides the proxy URL
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    public function getProxyUrl($model)
    {
        return URL::to('/' . $this->proxyUrl() . '/' . $model->uuid);
    }

    /**
     * @param $public
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getStorageDisk($public): Filesystem
    {
        return Storage::disk($public ? StorageLocations::S3_PUBLIC : StorageLocations::S3_PRIVATE);
    }
}
