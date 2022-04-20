<?php

namespace Rhf\Modules\Development\Controllers;

use Carbon\Carbon;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Development\Requests\MedalsCreationRequest;
use Rhf\Modules\Development\Requests\UserActivitiesRequest;
use Rhf\Modules\Development\Services\ActivitiesService;
use Rhf\Modules\Development\Services\UserService;
use Rhf\Modules\User\Models\User;

class ActivitiesController extends Controller
{
    public function __construct(
        UserService $userService,
        ActivitiesService $activitiesService
    ) {
        $this->userService = $userService;
        $this->activitiesService = $activitiesService;
    }

    public function createActivitesForUserBetweenDates(UserActivitiesRequest $request)
    {
        $startDate = $request->json('start_date');
        $endDate = $request->json('end_date');
        $userId = $request->json('user_id');
        $types = $request->json('types');

        $user = User::where('id', $userId)->first();
        if ($user->active == false) {
            return response()->noContent();
        };

        $recordsCreated = $this->activitiesService->createUsersTestActivity(
            Carbon::parse($startDate),
            Carbon::parse($endDate),
            $user,
            $types
        );

        return response()->json(
            [
                'data' => [
                    'activity_index' => $recordsCreated[0]->id,
                    'activities_count' => $recordsCreated,
                ]
            ]
        );
    }

    public function setDailyMedal(MedalsCreationRequest $request)
    {
        $medals = $request->json('medals');

        $userId = $request->json('user_id');
        $user = User::where('id', $userId)->first();

        if ($user->active == false) {
            return response()->noContent();
        };

        foreach ($medals as $medal) {
            $medalWanted = $medal['type'];
            foreach ($medal['dates'] as $date) {
                if (Carbon::parse($date)->lte(Carbon::parse($user->created_at))) {
                    return response()->noContent();
                }
                $date =  Carbon::parse($date)->startOfDay();
                $starsNeeded = $this->activitiesService->getStarsNeededForMedal($medalWanted);
                $this->activitiesService->generateStars($date, $user, $starsNeeded);
            }
        }
        return response(null, 201);
    }
}
