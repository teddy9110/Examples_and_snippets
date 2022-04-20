<?php

namespace Rhf\Modules\User\Controllers;

use Carbon\Carbon;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Activity\Filters\ActivityFilter;
use Rhf\Modules\Activity\Services\ActivitiesService;

class CarbQuestionnaireController extends Controller
{
    public function __construct(ActivitiesService $activitiesService)
    {
        $this->activitiesService = $activitiesService;
    }

    public function gender()
    {
        $user = auth('api')->user()->preferences;
        return response()->json([
            'data' => [
                'gender' => $user->gender,
            ]
        ], 200);
    }

    public function carbGoal()
    {
        $user = auth('api')->user()->preferences;
        return response()->json([
            'data' => [
                'target' => $user->daily_carbohydrate_goal
            ]
        ], 200);
    }

    public function weeklyAverage($type)
    {
        $now = Carbon::now();
        $subWeek = $now->copy()->subWeek();
        $subTwoWeeks = $now->copy()->subWeeks(2);

        $filters['type'] = $type;
        $filters['range']['start_date'] = $subTwoWeeks;
        $filters['range']['end_date'] = $now;

        $activity = $this->activitiesService->getActivities(new ActivityFilter($filters), []);

        return response()->json([
            'data' => [
                'week-1-average' => $this->activitiesService->getAveragesBetweenDates($activity, $now, $subWeek),
                'week-2-average' => $this->activitiesService->getAveragesBetweenDates(
                    $activity,
                    $subWeek,
                    $subTwoWeeks
                ),
            ]
        ]);
    }
}
