<?php

namespace Rhf\Modules\WebForm\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Rhf\Enums\StorageLocations;
use Rhf\Modules\Recipe\Services\RecipeImageFileService;
use Rhf\Modules\WebForm\Models\TransformationStory;

class TransformationService
{
    protected $transformationStories;

    public function __construct(TransformationStory $transformationStory)
    {
        $this->transformationStories = $transformationStory;
    }

    /**
     * Create Transformation
     *
     * @param $data
     * @param $beforeImage
     * @param $afterImage
     * @return mixed
     */
    public function createTransformation($data, $beforeImage, $afterImage)
    {
        // remove images from array
        unset($data['before_photo']);
        unset($data['after_photo']);
        $data['last_name'] = $data['second_name'];
        $data['marketing_accepted'] = $data['marketing_accepted'] === "true" ? 1 : 0;
        $data['remain_anonymous'] = $data['remain_anonymous'] === "true" ? 1 : 0;
        unset($data['second_name']);

        // create story
        $story = $this->transformationStories->create($data);
        $beforeImage = $this->storeImage($beforeImage, $story->id);
        $afterImage =  $this->storeImage($afterImage, $story->id);

        $story->update([
            'before_photo' => $beforeImage['path'] . '/' . $beforeImage['file_name'],
            'after_photo' => $afterImage['path'] . '/' . $afterImage['file_name'],
        ]);

        return $story;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|TransformationStory[]
     */
    public function getAll($page)
    {
        return $this->transformationStories->paginate(20, '*', 'page', $page);
    }

    /**
     * Return User Transformation Story
     *
     * @param $id
     * @return mixed
     */
    public function getTransformation($id)
    {
        return $this->transformationStories->findOrFail($id);
    }

    public function deleteTransformation($id)
    {
        $transformation = $this->getTransformation($id);
        $this->deleteImage($transformation);
        return $transformation->delete();
    }

    /**
     * @return mixed
     */
    public function getTransformationImage($image)
    {
        return $this
            ->getStorageDisk()
            ->temporaryUrl(
                config('filesystems.disks.spaces.namespace') . '/' . $image,
                Carbon::now()->addMinutes(60)
            );
    }

    /**
     * Store an image on DigitalOcean
     *
     * @param UploadedFile $image
     * @param $storyId
     * @return array
     * @throws \Exception
     */
    private function storeImage(UploadedFile $image, $storyId): array
    {
        $fileService = new RecipeImageFileService();
        $imagePath = $fileService->createFromUpload(
            $image,
            'user-stories' . '/' . $storyId,
            false
        );
        return $imagePath;
    }

    /**
     * Delete images of user
     *
     * @param $id
     */
    private function deleteImage(TransformationStory $transformation)
    {
        $this->getStorageDisk()->delete([
            config('filesystems.disks.spaces.namespace') . '/' . $transformation->before_photo,
            config('filesystems.disks.spaces.namespace') . '/' . $transformation->after_photo
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getStorageDisk(): Filesystem
    {
        return Storage::disk(StorageLocations::SPACES);
    }
}
