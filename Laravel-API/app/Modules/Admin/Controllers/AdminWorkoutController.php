<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminSyncRelatedVideosRequest;
use Rhf\Modules\Admin\Requests\AdminThumbnailRequest;
use Rhf\Modules\Admin\Requests\AdminVideoRequest;
use Rhf\Modules\Admin\Requests\AdminWorkoutRequest;
use Rhf\Modules\Admin\Resources\AdminWorkoutResource;
use Rhf\Modules\Workout\Models\Workout;
use Rhf\Modules\Workout\Services\WorkoutService;

class AdminWorkoutController extends Controller
{
    /**
     * @var WorkoutService
     */
    private $workoutService;

    public function __construct(WorkoutService $workoutService)
    {
        $this->workoutService = $workoutService;
    }

    /**
     * Get paginated workouts
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filter = Arr::wrap($request->input('filter', []));


        $query = Workout::query()
            ->orderBy($orderBy, $orderDirection);


        $equalsFilters = Arr::only($filter, ['exercise_location_id', 'exercise_level_id', 'exercise_frequency_id']);
        foreach ($equalsFilters as $key => $value) {
            if (isset($value) && $value != '') {
                $query->where($key, $value);
            }
        }

        if (Arr::has($filter, 'title')) {
            $t = $filter['title'];
            $query->where(function ($q) use ($t) {
                $q->where('title', 'like', "%$t%")->orWhere('descriptive_title', 'like', "%$t%");
            });
        }

        return AdminWorkoutResource::collection($query->paginate($perPage));
    }

    /**
     * Get specific workout
     *
     * @param $id
     * @return AdminWorkoutResource
     */
    public function show($id, Request $request)
    {
        $exercise = Workout::with([
            'frequency',
            'level',
            'location',
            'rounds.roundExercises',
            'promotedProduct',
            'relatedVideos',
        ])->findOrFail($id);
        return new AdminWorkoutResource($exercise);
    }

    /**
     * Create a new exercise
     *
     * @param AdminWorkoutRequest $request
     * @return AdminWorkoutResource
     */
    public function store(AdminWorkoutRequest $request)
    {
        $this->workoutService->setWorkout(new Workout());
        try {
            $this->storeWorkout($request);
        } catch (\Exception $e) {
            $message = config('app.debug') ? $e->getMessage() : 'Error creating workout';
            throw new FitnessHttpException($message, 500);
        }

        return new AdminWorkoutResource($this->workoutService->getWorkout());
    }

    /**
     * Update a workout
     *
     * @param AdminWorkoutRequest $request
     * @param $id
     * @return AdminWorkoutResource
     */
    public function update(AdminWorkoutRequest $request, $id)
    {
        $this->workoutService->setWorkout(Workout::findOrFail($id));
        try {
            $this->storeWorkout($request);
        } catch (\Exception $e) {
            $message = config('app.debug') ? $e->getMessage() : 'Error updating workout';
            throw new FitnessHttpException($message, 500);
        }
        return new AdminWorkoutResource($this->workoutService->getWorkout());
    }

    /**
     * Update the given exercise thumbnail
     *
     * @param AdminThumbnailRequest $request
     * @param $id
     * @return AdminWorkoutResource
     * @throws Exception
     */
    public function updateThumbnail(AdminThumbnailRequest $request, $id)
    {
        $workout = Workout::findOrFail($id);
        $this->workoutService->setWorkout($workout);
        $this->workoutService->setThumbnail($request->file('thumbnail'), $request->input('thumbnail_type'));
        $this->workoutService->persist();
        return new AdminWorkoutResource($this->workoutService->getWorkout());
    }

    /**
     * Update the given exercise video
     *
     * @param AdminVideoRequest $request
     * @param $id
     * @return AdminWorkoutResource
     * @throws Exception
     */
    public function updateVideo(AdminVideoRequest $request, $id)
    {
        $workout = Workout::findOrFail($id);
        $this->workoutService->setWorkout($workout);
        $this->workoutService->setVideo($request->file('video'));
        $this->workoutService->persist();
        return new AdminWorkoutResource($this->workoutService->getWorkout());
    }

    /**
     * Delete a workout.
     *
     * @param Request $request
     * @param $id
     */
    public function destroy(Request $request, $id)
    {
        $workout = Workout::findOrFail($id);
        $workout->delete();
        return response()->noContent();
    }

        /**
     * Store workout data from request.
     *
     * @param AdminWorkoutRequest $request
     * @return void
     */
    private function storeWorkout(AdminWorkoutRequest $request)
    {
        $flow = $request->input('workout_flow');

        switch ($flow) {
            case Workout::FLOW_YOUTUBE:
                DB::transaction(function () use ($request) {
                    $this->workoutService
                        ->setFillableData($request->only([
                            'workout_flow',
                            'title',
                            'descriptive_title',
                            'content',
                            'order',
                            'youtube',
                            'duration',
                        ]))
                        ->setFrequency($request->input('exercise_frequency_id'))
                        ->setLocation($request->input('exercise_location_id'))
                        ->setPromotedProduct($request->input('promoted_product_id'))
                        ->persistIfDoesNotExist();

                    if (!is_null($thumbnail = $request->file('thumbnail'))) {
                        $this->workoutService->setThumbnail($thumbnail, 'content_thumbnail');
                    }

                    if ($request->has('related_videos')) {
                        $this->workoutService->syncRelatedVideos($this->getRelatedVideos($request));
                    }

                    $this->workoutService->persist();
                });
                break;
            case Workout::FLOW_STANDARD:
                DB::transaction(function () use ($request) {
                    $this->workoutService
                        ->setFillableData($request->only(
                            'workout_flow',
                            'title',
                            'descriptive_title',
                            'content',
                            'order',
                        ))
                        ->setFrequency($request->input('exercise_frequency_id'))
                        ->setLevel($request->input('exercise_level_id'))
                        ->setLocation($request->input('exercise_location_id'))
                        ->setPromotedProduct($request->input('promoted_product_id'))
                        ->persistIfDoesNotExist()
                        ->setRounds($request->input('rounds', []));

                    if (!is_null($thumbnail = $request->file('thumbnail'))) {
                        $this->workoutService->setThumbnail($thumbnail, 'content_thumbnail');
                    }
                    if (!is_null($thumbnail = $request->file('standard_flow_thumbnail'))) {
                        $this->workoutService->setThumbnail($thumbnail, 'standard_flow_thumbnail');
                    }
                    if (!is_null($thumbnail = $request->file('youtube_flow_thumbnail'))) {
                        $this->workoutService->setThumbnail($thumbnail, 'youtube_flow_thumbnail');
                    }
                    if (!is_null($video = $request->file('video'))) {
                        $this->workoutService->setVideo($video);
                    }

                    $this->workoutService->persist();
                });
                break;
        }
    }

    public function syncRelatedVideos(AdminSyncRelatedVideosRequest $request, $id)
    {
        $workout = Workout::findOrFail($id);

        $this->workoutService
            ->setWorkout($workout)
            ->syncRelatedVideos($this->getRelatedVideos($request));

        return new AdminWorkoutResource($this->workoutService->getWorkout());
    }

    private function getRelatedVideos(Request $request)
    {
        return array_replace_recursive(
            $request->input('related_videos', []),
            $request->file('related_videos', [])
        );
    }
}
