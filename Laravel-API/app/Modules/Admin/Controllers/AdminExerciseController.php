<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminExerciseRequest;
use Rhf\Modules\Admin\Requests\AdminThumbnailRequest;
use Rhf\Modules\Admin\Requests\AdminVideoRequest;
use Rhf\Modules\Admin\Resources\AdminExerciseFrequencyResource;
use Rhf\Modules\Admin\Resources\AdminExerciseLevelResource;
use Rhf\Modules\Admin\Resources\AdminExerciseLocationResource;
use Rhf\Modules\Admin\Resources\AdminExerciseResource;
use Rhf\Modules\Workout\Models\ExerciseLevel;
use Rhf\Modules\Workout\Models\ExerciseLocation;
use Rhf\Modules\Workout\Models\Exercise;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Services\ExerciseService;

class AdminExerciseController extends Controller
{
    /**
     * @var ExerciseService
     */
    private $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    /**
     * Get paginated exercises
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filterBy = $request->input('filter_by');
        $filterValue = $request->input('filter');

        $query = Exercise::query()
            ->orderBy($orderBy, $orderDirection);

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        if ($request->input('show_all', false)) {
            return AdminExerciseResource::collection($query->get());
        }

        return AdminExerciseResource::collection($query->paginate($perPage));
    }

    /**
     * Get specific exercise
     *
     * @param $id
     * @return AdminExerciseResource
     */
    public function show($id, Request $request)
    {
        $exercise = Exercise::findOrFail($id);
        return new AdminExerciseResource($exercise);
    }

    /**
     * Create a new exercise
     *
     * @param AdminExerciseRequest $request
     * @return AdminExerciseResource
     */
    public function store(AdminExerciseRequest $request)
    {
        $exercise = $this->exerciseService->createExercise(
            $request->only('title', 'descriptive_title', 'content', 'quantity', 'sort_order'),
            $request->file('thumbnail'),
            $request->file('video')
        );
        return new AdminExerciseResource($exercise);
    }

    /**
     * Create a new exercise
     *
     * @param AdminExerciseRequest $request
     * @param $id
     * @return AdminExerciseResource
     */
    public function update(AdminExerciseRequest $request, $id)
    {
        $this->exerciseService->setExercise(Exercise::findOrFail($id));
        $exercise = $this->exerciseService->updateExercise(
            $request->only('title', 'descriptive_title', 'content', 'quantity', 'sort_order'),
            $request->file('thumbnail'),
            $request->file('video')
        );
        return new AdminExerciseResource($exercise);
    }

    /**
     * Delete an exercise.
     *
     * @param Request $request
     * @param $id
     */
    public function destroy(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->workoutRoundExercises()->delete();
        $exercise->delete();
        return response()->noContent();
    }

    /**
     * Fetch exercise frequencies
     *
     * @return AnonymousResourceCollection
     */
    public function frequencies()
    {
        $frequencies = ExerciseFrequency::all();
        return AdminExerciseFrequencyResource::collection($frequencies);
    }

    /**
     * Fetch exercise locations
     *
     * @return AnonymousResourceCollection
     */
    public function locations()
    {
        $locations = ExerciseLocation::all();
        return AdminExerciseLocationResource::collection($locations);
    }

    /**
     * Fetch exercise levels
     *
     * @return AnonymousResourceCollection
     */
    public function levels()
    {
        $levels = ExerciseLevel::all();
        return AdminExerciseLevelResource::collection($levels);
    }

    /**
     * Update the given exercise thumbnail
     *
     * @param AdminThumbnailRequest $request
     * @param $id
     * @return JsonResource
     * @throws Exception
     */
    public function updateThumbnail(AdminThumbnailRequest $request, $id)
    {
        $exercise = Exercise::findOrFail($id);
        $this->exerciseService->setExercise($exercise);
        $this->exerciseService->setThumbnail($request->file('thumbnail'), true);
        return new AdminExerciseResource($this->exerciseService->getExercise());
    }

    /**
     * Update the given exercise video
     *
     * @param AdminVideoRequest $request
     * @param $id
     * @return JsonResource
     * @throws Exception
     */
    public function updateVideo(AdminVideoRequest $request, $id)
    {
        $exercise = Exercise::findOrFail($id);
        $this->exerciseService->setExercise($exercise);
        $this->exerciseService->setVideo($request->file('video'), true);
        return new AdminExerciseResource($this->exerciseService->getExercise());
    }
}
