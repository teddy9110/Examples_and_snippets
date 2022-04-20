<?php

namespace Rhf\Modules\Exercise\Controllers;

use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Services\UserWorkoutService;

/**
 * DEPRECATED:
 */
class ExerciseController extends Controller
{
    /**
     * Create a new ExerciseController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the available exercise categories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => ExerciseCategory::get()]);
    }

    /**
     * Get the available exercise levels.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function levels(Request $request)
    {
        return response()->json(['status' => 'success', 'data' => ExerciseLevel::get()]);
    }

    /**
     * Get the workouts/categories for the current user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCategories(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        try {
            // Check if we have an exercise level
            if ($user->hasPreference('exercise_level_id')) {
                $userWorkoutService = new UserWorkoutService($user);
                $categories = $userWorkoutService->calculateWorkouts();
            } else {
                throw new FitnessPreconditionException(
                    'Unable to retrieve workouts. Exercise level not set. Please contact Team RH Support.'
                );
            }
        } catch (\Exception $e) {
            throw new FitnessPreconditionException($e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    /**
     * Get the a workout based on current user and category.
     *
     * @param Request $request
     * @param $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function workout(Request $request, $category_id)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        try {
            // Check if we have an exercise level
            if (
                $user->hasPreference('exercise_level_id')
                && $user->hasPreference('exercise_frequency_id')
                && $user->hasPreference('exercise_location_id')
            ) {
                $userWorkoutService = new UserWorkoutService($user);
                $workout = $userWorkoutService->getWorkout($category_id);
            } else {
                throw new FitnessPreconditionException(
                    'Unable to retrieve workouts. Exercise level not set. Please contact Team RH Support.'
                );
            }
        } catch (\Exception $e) {
            throw new FitnessPreconditionException($e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $workout]);
    }
}
