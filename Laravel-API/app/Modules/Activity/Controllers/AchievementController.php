<?php

namespace Rhf\Modules\Activity\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Activity\Models\AchievementDay;
use Rhf\Modules\Activity\Models\AchievementWeek;
use Rhf\Modules\Activity\Requests\MedalsRequest;
use Rhf\Modules\Activity\Resources\DailyAchievementResource;
use Rhf\Modules\Activity\Resources\WeeklyAchievementResource;
use Rhf\Modules\Activity\Services\AchievementService;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\MyFitnessPal\Services\DiaryService;
use Rhf\Modules\System\Resources\PaginationResource;

class AchievementController extends Controller
{
    /**
     * Get achievement medal for given date.
     *
     * @param Request $request
     * @param date to retrieve for
     *
     * @return JsonResponse
     */
    public function medalByDay(Request $request, $date)
    {
        // Validate dates
        if (Carbon::now()->addDays(1)->lte($date)) {
            throw new FitnessBadRequestException('Invalid Date: Please select a date in the past.');
        }

        try {
            /** @var \Rhf\Modules\User\Models\User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync(Carbon::parse($date));
            }

            $from = Carbon::parse($date)->startOfDay();
            $to = Carbon::parse($date)->endOfDay();

            $activityService = new ActivityService();
            $activities = $activityService->setUser($user)
                ->from($from)
                ->to($to)
                ->retrieve()
                ->get();

            $achievementDay = new AchievementDay($from, $user, $activities);
            $achievement = new DailyAchievementResource($achievementDay);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve daily medal.');
        }

        return response()->json(['status' => 'success', 'data' => $achievement]);
    }

    /**
     * Get achievement medal for given week.
     * Replaced by Historical Medals, Removed from apps after July.
     * Marked for Deprecation.
     * @deprecated 1.7.0
     * @param Request $request
     * @param date to retrieve for
     *
     * @return JsonResponse
     */
    public function medalByWeek(Request $request, $date)
    {
        // Validate dates
        if (Carbon::now()->subDays(6)->lt($date)) {
            throw new FitnessBadRequestException('Invalid Date: Please select a previous week.');
        }

        try {
            /** @var \Rhf\Modules\User\Models\User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync(Carbon::parse($date));
            }

            $from = Carbon::parse($date)->startOfDay();
            $to = Carbon::parse($date)->endOfWeek();

            $activityService = new ActivityService();
            $activities = $activityService->setUser($user)
                ->from($from)
                ->to($to)
                ->retrieve()
                ->get();

            $achievementWeek = new AchievementWeek($from, $user, $activities);
            $achievement = new WeeklyAchievementResource($achievementWeek);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve weekly medal.');
        }

        return response()->json(['status' => 'success', 'data' => $achievement]);
    }

    /**
     * Get achievement medal overview
     * Replaced by Historical Medals, Removed from apps after July.
     * Marked for Deprecation.
     * @deprecated 1.7.0
     * @return JsonResponse
     */
    public function overview()
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        try {
            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync(now()->startOfDay());
                DiaryService::sync(now()->subDay(1)->startOfDay());
                DiaryService::sync(now()->subWeek(1)->startOfDay());
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve medal overview.');
        }

        $activityService = new ActivityService();
        $date = Carbon::now();

        $activities = $activityService->setUser($user)
            ->from($date->clone()->startOfDay()->subDays(6))
            ->to($date->clone()->endOfDay())
            ->retrieve()
            ->get();

        $achievementLastWeek = new AchievementWeek(
            $date->clone()->startOfDay()->subWeek(1),
            $user,
            $activities,
        );

        $achievementYesterday = new AchievementDay(
            $date->clone()->startOfDay()->subDay(),
            $user,
            $activities->where('date', $date->clone()->subDay()->startOfDay()),
            $activities
        );

        $achievementToday = new AchievementDay(
            $date->startOfDay(),
            $user,
            $activities->where('date', $date->clone()->startOfDay()),
            $activities
        );

        return response()->json([
            'data' => [
                'last_week' => new WeeklyAchievementResource($achievementLastWeek),
                'yesterday' => new DailyAchievementResource($achievementYesterday),
                'today' => new DailyAchievementResource($achievementToday)
            ]
        ]);
    }

    /**
     * Historical Medals - Changed 2021-09-23
     *
     * @param MedalsRequest $request
     * @return PaginationResource
     */
    public function historicalMedals(MedalsRequest $request)
    {
        $page = intval($request->input('page', 1));
        $perPage = intval($request->input('per_page', 5));
        $period = $request->input('group_by', 'week');
        $sortDirection = $request->input('sort_direction', 'desc');  // Default to desc, to support older app versions

        $user = auth('api')->user();

        $startDate = $request->has('start_date') ?
            Carbon::parse($request->input('start_date')) :
            $user->created_at;

        $endDate = $request->has('end_date') ?
            Carbon::parse($request->input('end_date')) :
            Carbon::now();

        $totalCount = iterator_count(get_period_range($startDate, $endDate, $period));
        if ($page > ceil($totalCount / $perPage)) {
            $data = paginate([], $totalCount, $perPage, $page);
            return new PaginationResource($data);
        }

        $range = get_pagination_period_range(
            $page,
            $perPage,
            $startDate,
            $endDate,
            $period,
            $sortDirection
        );

        $achievementService = new AchievementService();
        $achievementService->setUser($user);
        $medals = $achievementService->getMedalsForRange($range, $period, $sortDirection);
        $data = paginate($medals, $totalCount, $perPage, $page);

        return new PaginationResource($data);
    }
}
