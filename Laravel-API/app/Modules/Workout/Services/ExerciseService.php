<?php

namespace Rhf\Modules\Workout\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Modules\Workout\Models\Exercise;
use Rhf\Modules\Workout\Services\WorkoutFileService;

class ExerciseService
{
    protected $exercise = null;

    /**
     * Return the item associated to the instance of the service.
     *
     * @return Exercise
     */
    public function getExercise()
    {
        return $this->exercise;
    }

    /**
     * Set the exercise associated to the instance of the service.
     *
     * @param Exercise $exercise
     * @return self
     */
    public function setExercise(Exercise $exercise)
    {
        $this->exercise = $exercise;
        return $this;
    }

    /**
     * Create an exercise
     */
    public function createExercise(array $data, UploadedFile $thumbnail = null, UploadedFile $video = null)
    {
        $exercise = new Exercise();
        $this->setExercise($exercise);
        return $this->updateExercise($data, $thumbnail, $video);
    }

    /**
     * Update an exercise
     *
     * @param array $data
     * @param UploadedFile $thumbnail
     * @param UploadedFile $video
     * @throws \Exception
     */
    public function updateExercise(array $data, UploadedFile $thumbnail = null, UploadedFile $video = null)
    {
        DB::beginTransaction();

        try {
            $exercise = $this->getExercise();
            $exercise->fill($data);

            if (isset($thumbnail)) {
                $this->setThumbnail($thumbnail);
            }

            if (isset($video)) {
                $this->setVideo($video);
            }

            $exercise->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new FitnessHttpException('Error creating exercise', 500);
        }

        return $this->getExercise();
    }

    /**
     * Uploads an exercise thumbnail
     *
     * @param UploadedFile $image
     * @param bool $persist
     * @return void
     * @throws \Exception
     */
    public function setThumbnail(UploadedFile $image, $persist = false)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_thumbnail');
        $imagePath = $fileService->createFromUpload($image, 'exercise-thumbnails', false);

        if (isset($this->exercise->content_thumbnail)) {
            $this->deleteThumbnail();
        }

        $this->exercise->content_thumbnail = $imagePath['path'] . '/' . $imagePath['file_name'];
        $this->exercise->thumbnail = $imagePath['file_name'];

        if ($persist) {
            $this->exercise->save();
        }
    }

    /**
     * Deletes an exercise thumbnail
     *
     * @param bool $persist
     * @return void
     * @throws \Exception
     */
    public function deleteThumbnail($persist = false)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_thumbnail');
        if ($fileService->delete($this->exercise)) {
            $this->exercise->thumbnail = null;
            $this->exercise->content_thumbnail = null;
        }

        if ($persist) {
            $this->exercise->save();
        }
    }

    /**
     * Uploads an exercise video
     *
     * @param UploadedFile $video
     * @param bool $persist
     * @return void
     * @throws \Exception
     */
    public function setVideo(UploadedFile $video, $persist = false)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_video');
        $videoPath = $fileService->createFromUpload($video, 'exercise-videos', false);

        if (isset($this->exercise->content_video)) {
            $this->deleteVideo();
        }

        $this->exercise->content_video = $videoPath['path'] . '/' . $videoPath['file_name'];
        $this->exercise->video = $videoPath['file_name'];

        if ($persist) {
            $this->exercise->save();
        }
    }

    /**
     * Deletes an exercise video
     *
     * @param bool $persist
     * @return void
     * @throws \Exception
     */
    public function deleteVideo($persist = false)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_video');
        if ($fileService->delete($this->exercise)) {
            $this->exercise->video = null;
            $this->exercise->content_video = null;
        }

        if ($persist) {
            $this->exercise->save();
        }
    }
}
