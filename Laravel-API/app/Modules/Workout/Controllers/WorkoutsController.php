<?php

namespace Rhf\Modules\Workout\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Exercise\Controllers\ExerciseController;
use Rhf\Modules\Workout\Models\ExerciseLocation;
use Rhf\Modules\Workout\Models\Workout;
use Rhf\Modules\Workout\Resources\WorkoutResource;
use Rhf\Modules\Workout\Services\UserWorkoutService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkoutsController extends Controller
{
    public function userWorkouts(Request $request)
    {
        if (!api_version() || !grhaft_enabled()) {
            // DEPRECATED: To be deprecated
            $exerciseController = app(ExerciseController::class);
            return $exerciseController->userCategories($request);
        }
        /** @var \Rhf\Modules\User\Models\User $user*/
        $user = auth('api')->user();

        if (!$user->hasPreferences(['exercise_frequency_id', 'exercise_location_id'])) {
            throw new FitnessPreconditionException(
                'Error: Unable to retrieve workouts. ' .
                'Exercise location or frequency not set. Please contact Team RH Support.'
            );
        }

        if (!workouts_v3_available()) {
            $location = ExerciseLocation::findOrFail($user->getPreference('exercise_location_id'));
            if ($location->slug === ExerciseLocation::SLUG_HOME && !$user->hasPreference('exercise_level_id')) {
                throw new FitnessPreconditionException(
                    'Error: Unable to retrieve workouts. Exercise level not set. Please contact Team RH Support.'
                );
            }
        }

        $service = new UserWorkoutService($user);
        $workouts = $service->orderWorkoutsBySchedule($service->getUserWorkouts());
        return WorkoutResource::collection($workouts);
    }

    public function workout(Request $request, $id)
    {
        $workout = Workout::with([
            'rounds.roundExercises.exercise',
            'promotedProduct',
            'relatedVideos',
        ])->findOrFail($id);
        return new WorkoutResource($workout);
    }

    public function workoutByDate($date)
    {
        /** @var \Rhf\Modules\User\Models\User $user*/
        $user = auth('api')->user();
        $day = Carbon::parse($date)->dayOfWeek;
        $service = new UserWorkoutService($user);
        $workouts = $service->orderWorkoutsBySchedule($service->getUserWorkouts());
        if (!$workouts->has($day)) {
            throw new NotFoundHttpException();
        }
        return new WorkoutResource($workouts->get($day));
    }
}
