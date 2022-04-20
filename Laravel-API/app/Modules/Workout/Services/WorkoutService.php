<?php

namespace Rhf\Modules\Workout\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Rhf\Modules\Product\Models\PromotedProduct;
use Rhf\Modules\Video\Services\RelatedVideoService;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Models\ExerciseLevel;
use Rhf\Modules\Workout\Models\ExerciseLocation;
use Rhf\Modules\Workout\Models\Round;
use Rhf\Modules\Workout\Models\RoundExercise;
use Rhf\Modules\Workout\Models\Workout;
use Rhf\Modules\Workout\Services\WorkoutFileService;

class WorkoutService
{
    protected RelatedVideoService $relatedVideoService;

    protected $workout = null;

    public function __construct(RelatedVideoService $relatedVideoService)
    {
        $this->relatedVideoService = $relatedVideoService;
    }

    /**
     * Return the item associated to the instance of the service.
     *
     * @return Workout
     */
    public function getWorkout()
    {
        return $this->workout;
    }

    /**
     * Set the workout associated to the instance of the service.
     *
     * @param Workout $workout
     * @return self
     */
    public function setWorkout(Workout $workout)
    {
        $this->workout = $workout;
        return $this;
    }

    /**
     * Update fillable workout data
     *
     * @param array $data
     * @return self
     */
    public function setFillableData(array $data)
    {
        $this->workout->fill($data);
        return $this;
    }

    /**
     * Update workout rounds.
     * Creates new rounds, updates existing ones and deletes missing.
     *
     * @param array $rounds
     * @return self
     */
    public function setRounds(array $rounds = [])
    {
        $existingRoundIds = $this->workout->rounds->pluck('id')->toArray();
        $currentRoundIds = [];

        foreach ($rounds as $round) {
            $model = isset($round['id']) ? Round::find($round['id']) : new Round();
            $model->fill(Arr::only($round, ['title', 'content', 'order']));
            $model->workout_id = $this->workout->id;
            $model->save();

            $currentRoundIds[] = $model->id;

            $this->setRoundExercises($model, $round['roundExercises'] ?? []);
        }

        $roundIdsToDelete = array_diff($existingRoundIds, $currentRoundIds);
        Round::whereIn('id', $roundIdsToDelete)->delete();

        return $this;
    }

    /**
     * Update exercises for a round.
     * Creates new round exercises, updates existing ones and deletes missing.
     *
     * @param Round $round
     * @param array $roundExercises
     * @return void
     */
    private function setRoundExercises(Round $round, array $roundExercises)
    {
        $existingExerciseIds = isset($round['id']) ? $round->roundExercises->pluck('id')->toArray() : [];
        $currentExerciseIds = [];

        foreach ($roundExercises as $roundExercise) {
            $model = isset($roundExercise['id']) ? RoundExercise::find($roundExercise['id']) : new RoundExercise();
            $model->fill(Arr::only($roundExercise, ['quantity', 'order']));
            $model->exercise_id = $roundExercise['exercise_id'];
            $model->round_id = $round->id;
            $model->save();

            $currentExerciseIds[] = $model->id;
        }

        $exerciseIdsToDelete = array_diff($existingExerciseIds, $currentExerciseIds);
        RoundExercise::whereIn('id', $exerciseIdsToDelete)->delete();
    }

    /**
     * Set exercise frequency
     *
     * @param $frequencyId
     * @return self
     */
    public function setFrequency($frequencyId)
    {
        if (is_null($frequencyId)) {
            $this->workout->frequency()->dissociate();
        } else {
            $this->workout->frequency()->associate(ExerciseFrequency::findOrFail($frequencyId));
        }
        return $this;
    }

    /**
     * Set exercise level
     *
     * @param $levelId
     * @return self
     */
    public function setLevel($levelId)
    {
        if (is_null($levelId)) {
            $this->workout->level()->dissociate();
        } else {
            $this->workout->level()->associate(ExerciseLevel::findOrFail($levelId));
        }
        return $this;
    }

    /**
     * Set exercise location
     *
     * @param $locationId
     * @return self
     */
    public function setLocation($locationId)
    {
        if (is_null($locationId)) {
            $this->workout->location()->dissociate();
        } else {
            $this->workout->location()->associate(ExerciseLocation::findOrFail($locationId));
        }
        return $this;
    }

    /**
     * Set promoted product
     *
     * @param $productId
     * @return self
     */
    public function setPromotedProduct($productId)
    {
        if (is_null($productId)) {
            $this->workout->promotedProduct()->dissociate();
        } else {
            $this->workout->promotedProduct()->associate(PromotedProduct::find($productId));
        }
        return $this;
    }

    /**
     * Uploads a workout thumbnail
     *
     * @param UploadedFile $image
     * @throws \Exception
     * @return self
     */
    public function setThumbnail(UploadedFile $image, $thumbnailType)
    {
        $fileService = new WorkoutFileService();
        if (isset($this->workout->{$thumbnailType})) {
            $this->deleteThumbnail($thumbnailType);
        }
        $fileService->setFileAttribute($thumbnailType);
        $imagePath = $fileService->createFromUpload($image, 'workout-thumbnails', false);
        $this->workout->{$thumbnailType} = $imagePath['path'] . '/' . $imagePath['file_name'];
        if ($thumbnailType == 'content_thumbnail') {
            $this->workout->thumbnail = $imagePath['file_name'];
        }
        return $this;
    }

    /**
     * Deletes a workout thumbnail
     *
     * @return void
     * @throws \Exception
     * @return self
     */
    public function deleteThumbnail($thumbnailType)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute($thumbnailType);
        if ($fileService->delete($this->workout)) {
            $this->workout->{$thumbnailType} = null;
            if ($thumbnailType == 'content_thumbnail') {
                $this->workout->thumbnail = null;
            }
        }
        return $this;
    }

    /**
     * Uploads a workout video
     *
     * @param UploadedFile $video
     * @throws \Exception
     * @return self
     */
    public function setVideo(UploadedFile $video)
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_video');
        $videoPath = $fileService->createFromUpload($video, 'workout-videos', false);

        if (isset($this->workout->content_video)) {
            $this->deleteVideo();
        }

        $this->workout->content_video = $videoPath['path'] . '/' . $videoPath['file_name'];
        $this->workout->video = $videoPath['file_name'];
        return $this;
    }

    /**
     * Deletes a workout video
     *
     * @return void
     * @throws \Exception
     * @return self
     */
    public function deleteVideo()
    {
        $fileService = new WorkoutFileService();
        $fileService->setFileAttribute('content_video');
        if ($fileService->delete($this->workout)) {
            $this->workout->video = null;
            $this->workout->content_video = null;
        }
        return $this;
    }

    /**
     * Save current workout
     *
     * @return self
     */
    public function persist()
    {
        $this->workout->save();
        return $this;
    }

    /**
     * Save current workout if it does not exist in the database
     *
     * @return self
     */
    public function persistIfDoesNotExist()
    {
        if (!$this->workout->exists) {
            $this->persist();
        }
        return $this;
    }

    /**
     * Sync related videos
     *
     * @param array $related
     * @return self
     */
    public function syncRelatedVideos(array $relatedVideos = [])
    {
        $currentVideos = [];

        foreach ($relatedVideos as $video) {
            $createdVideo = $this->relatedVideoService->createVideo(
                $video,
                $video['thumbnail'] ?? null,
                $this->workout->id
            );
            $this->workout->relatedVideos()->updateExistingPivot(
                $createdVideo->id,
                ['order' => $video['order']]
            );
            $currentVideos[] = $createdVideo->id;
        }

        $this->relatedVideoService->deleteVideoImages(
            array_diff($this->workout->relatedVideos->pluck('id')->toArray(), $currentVideos)
        );
        $this->workout->relatedVideos()
            ->whereNotIn('related_video_id', $currentVideos)
            ->delete();

        return $this;
    }
}
