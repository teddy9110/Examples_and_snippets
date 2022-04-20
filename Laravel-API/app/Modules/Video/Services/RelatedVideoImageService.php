<?php

namespace Rhf\Modules\Video\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\Recipe\Services\RecipeImageFileService;

class RelatedVideoImageService
{
    /**
     * Store an image against Competitions
     * Store entry nested under competition/id
     *
     * @param UploadedFile $thumbnail
     * @param $id
     * @param bool $entry
     * @return array
     * @throws \Exception
     */
    public function storeImage(UploadedFile $thumbnail, $id): array
    {
        $fileService = new RecipeImageFileService();
        $path = 'related-video/' . $id;
        $imagePath = $fileService->createFromUpload(
            $thumbnail,
            $path,
            true
        );
        return $imagePath;
    }

    /**
     * @return mixed
     */
    public function getImage($image)
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
     * Delete an Image
     */
    public function deleteImage($data)
    {
        if (!is_null($data->thumbnail)) {
            $this->getStorageDisk()->delete([
                config('filesystems.disks.spaces.namespace') . '/' . $data->thumbnail
            ]);
        }
    }
}
