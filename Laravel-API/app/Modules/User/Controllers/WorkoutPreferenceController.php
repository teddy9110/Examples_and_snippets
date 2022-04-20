<?php

namespace Rhf\Modules\User\Controllers;

use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Workout\Services\UserWorkoutService;

class WorkoutPreferenceController extends Controller
{
    public function setWorkoutPreferences(Request $request)
    {
        $request->validate(
            [
                'schedule' => 'nullable|array|size:7',
                'schedule.*' => 'gt:0|lte:7|distinct'
            ]
        );

        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $service = new UserWorkoutService($user);
        $service->saveWorkoutSchedule($request->json('schedule'));
        return response()->noContent();
    }

    public function getWorkoutPreferences()
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $service = new UserWorkoutService($user);
        return response()->json(
            [
                'data' => [
                    'schedule' => $service->retrieveWorkoutSchedule()
                ]
            ]
        );
    }
}
