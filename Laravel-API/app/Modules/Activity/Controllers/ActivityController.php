<?php

namespace Rhf\Modules\Activity\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Activity\Models\ActivityNotes;
use Rhf\Modules\Activity\Requests\ActivityLogRequest;
use Rhf\Modules\Activity\Requests\AverageLogRequest;
use Rhf\Modules\Activity\Requests\ExerciseActivityLogRequest;
use Rhf\Modules\Activity\Requests\StepsActivityLogRequest;
use Rhf\Modules\Activity\Requests\WaterActivityLogRequest;
use Rhf\Modules\Activity\Requests\WeightActivityLogRequest;
use Rhf\Modules\Activity\Requests\WorkoutActivityLogRequest;
use Rhf\Modules\Activity\Resources\ActivityNoteResource;
use Rhf\Modules\Activity\Resources\ActivityResource;
use Rhf\Modules\Activity\Services\ActivityCache;
use Rhf\Modules\Activity\Services\ActivityNoteService;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\Activity\Services\ProgressService;
use Rhf\Modules\Exercise\Models\ExerciseCategory;
use Rhf\Modules\MyFitnessPal\Services\DiaryService;
use Rhf\Modules\MyFitnessPal\Services\MyFitnessPalService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Services\UserService;
use Rhf\Modules\Workout\Models\Exercise;
use Rhf\Modules\Workout\Models\Workout;

class ActivityController extends Controller
{
    protected $activityNoteService;

    /**
     * Create a new ActivityController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->activityNoteService = new ActivityNoteService();
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyProgress($date)
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();

            // Update the user goals if necessary
            if ($user->needsGoals()) {
                $userService = new UserService();
                $userService->setUser(auth('api')->user())->updateGoals();
            }

            $date = Carbon::parse($date);

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $activityCache = new ActivityCache(auth('api')->user());
            $cacheKey = $activityCache->createCacheKey('daily_progress', $date->clone()->format('Y-m-d'));

            if (is_null($activityCache->getCache($cacheKey))) {
                $progress = $activityCache->cacheProgress($date);
            } else {
                $progress = json_decode($activityCache->getCache($cacheKey), true);
            }
        } catch (Exception $e) {
            report($e);
            throw new FitnessBadRequestException('Error: could not retrieve daily progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyFiberProgress($date)
    {
        try {
            $date = Carbon::parse($date);
            /** @var User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $progressService = new ProgressService();
            $progressService->setUser($user)->from($date)->to((clone $date));
            $progress = $progressService->dailyFiberProgress();
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not retrieve fiber progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyProteinProgress($date)
    {
        try {
            $date = Carbon::parse($date);
            /** @var User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $progressService = new ProgressService();
            $progressService->setUser($user)->from($date)->to((clone $date));
            $progress = $progressService->dailyProteinProgress();
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not retrieve protein progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyCaloriesProgress($date)
    {
        try {
            $date = Carbon::parse($date);
            /** @var User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $progressService = new ProgressService();
            $progressService->setUser($user)->from($date)->to((clone $date));
            $progress = $progressService->dailyCaloriesProgress();
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not retrieve calories progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyStepsProgress($date)
    {
        try {
            $date = Carbon::parse($date);
            /** @var User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $progressService = new ProgressService();
            $progressService->setUser($user)->from($date)->to((clone $date));
            $progress = $progressService->dailyStepsProgress();
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not retrieve steps progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param $date
     *
     * @return JsonResponse
     */
    public function dailyWaterProgress($date)
    {
        try {
            $date = Carbon::parse($date);
            /** @var User $user */
            $user = auth('api')->user();

            // Sync activity data across
            if ($user->hasConnectedMfp()) {
                DiaryService::sync($date);
            }

            $progressService = new ProgressService();
            $progressService->setUser($user)->from($date)->to((clone $date));
            $progress = $progressService->dailyWaterProgress();
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not retrieve water progress. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $progress]);
    }

    /**
     * Get an activity log by dates.
     *
     * @param string
     * @param string
     * @param string
     * @param object User
     * @param bool
     * @return JsonResponse
     */
    public function getLog($type, $startDate = null, $endDate = null, $user = null, $withWeeklyAverage = false)
    {
        // Check for dates
        if (!$startDate && !$endDate) {
            // Set the correct limit of days, if using MFP remote store limit to 7 days max
            $limit = 30;
            if (in_array($type, MyFitnessPalService::$dataTypes)) {
                $limit = 7;
            }
            $startDate = Carbon::now()->subDays($limit);
            $endDate = Carbon::now();
        }

        // Validate dates
        if ($startDate->gt($endDate)) {
            throw new FitnessBadRequestException('Error: end date cannot be greater than start date.');
        }

        // Check for user
        if (!$user) {
            $user = auth('api')->user();
        }

        try {
            // Sync activity data across
            if ($user->hasConnectedMfp() && in_array($type, MyFitnessPalService::$dataTypes)) {
                DiaryService::sync(Carbon::parse($startDate), Carbon::parse($endDate));
            }

            $isDailyLogType = in_array($type, Activity::$append);

            if (
                !$withWeeklyAverage && !$isDailyLogType && $this->isCacheableActivity($type) &&
                $activities = Redis::get($this->getCacheKey($type, $startDate, $endDate))
            ) {
                $activities = array_map(function ($item) {
                    // it returns integer, non-cached returns string - better safe than sorry
                    $item->value = (string) $item->value;
                    $a = new Activity((array) $item);
                    $a->id = $item->id;
                    return $a;
                }, json_decode($activities));

                $activities = $type == 'weight'
                    ? EloquentCollection::make($activities)->load('notes')
                    : collect($activities);

                return response()->json([
                    'status' => 'success',
                    'data' => ActivityResource::collection($activities->sortByDesc('date'))
                ]);
            }

            $activityService = new ActivityService();
            $log = $activityService->setUser($user)->from($startDate)->to($endDate)->byType($type);

            // Check if this is a daily log type
            if ($isDailyLogType) {
                $log = $log->dailyTotals();
            }

            // Check if we're retrieving an additional weekly average
            if ($withWeeklyAverage) {
                $log = ActivityResource::collection($log->getDailyAndWeekly());
            } else {
                $activities = $log->retrieve()->orderBy('date', 'ASC')->get();
                $log = ActivityResource::collection($activities);

                if (
                    !$isDailyLogType && $this->isCacheableActivity($type) &&
                    ($startDate->isSameDay($endDate) || $this->isStartAndEndOfSameWeek($startDate, $endDate))
                ) {
                    $key = $this->getCacheKey($type, $startDate, $endDate);
                    Redis::set($key, json_encode($activities), 'EX', 86400);
                }
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error:could not retrieve ' . $type . ' log. ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $log]);
    }

    /**
     * Wraps "getLog" for use by admin app.
     *
     * @param      $userId
     * @param      $type
     * @param null $startDate
     * @param null $endDate
     *
     * @return JsonResponse
     */
    public function getUserLog($userId, $type, $startDate = null, $endDate = null)
    {
        $user = User::find($userId);

        // Set the user in the API guard for use by services
        auth('api')->setUser($user);

        return $this->getLog($type, $startDate, $endDate, $user, true);
    }

    /**
     * Add a new log.
     *
     * @param      $date
     * @param      $type
     * @param      $value
     * @param null $calculationType
     *
     * @return JsonResponse
     * @throws Exception
     */
    protected function postLog($date, $type, $value, $calculationType = null, $details = [])
    {
        $date = Carbon::parse($date);
        if (Carbon::now()->addDays(1)->lte($date)) {
            throw new FitnessBadRequestException('Error: date is not valid or too far in the future.');
        }

        try {
            $activityService = new ActivityService();
            $activityService->setUser(auth('api')->user());
            $calculationType = is_null($calculationType) ? Activity::getCalculationType($type) : $calculationType;
            $activity = $activityService->createLog($type, $date, $value, $calculationType);
            if ($this->isCacheableActivity($type)) {
                $this->cache($this->getCacheKey($type, $date), [$activity]);
                $this->invalidateWeeklyCache($type, $date);
            }
            // Checks if should be notable
            $notable = $this->activityNoteService->shouldNote($type, $details);
            if ($notable) {
                $this->activityNoteService->createNote($activity->id, $date, $details);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: unable to create activity log. Please try again later.');
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove logs for a date.
     * @param string $type
     * @param string $date
     * @return JsonResponse
     */
    protected function deleteLog(string $type, string $date)
    {
        $date = Carbon::parse($date);

        try {
            $activityService = new ActivityService();
            $activityService->setUser(auth('api')->user());
            $logDeleteCount = $activityService->deleteLog($type, $date);

            if ($this->isCacheableActivity($type)) {
                // Delete daily cache
                Redis::del($this->getCacheKey($type, $date));
                $this->invalidateWeeklyCache($type, $date);
            }

            if ($logDeleteCount < 1) {
                return response(null, 404);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: unable to delete activity log. Please try again later.');
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Add a new exercise log.
     *
     * @param ExerciseActivityLogRequest $request
     * @param null $date
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postExercise(ExerciseActivityLogRequest $request, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $this->postLog($date, 'exercise', $request->input('exercise_id'));

        // THIS IS AN OVERRIDE TO FORCE A 200 RESPONSE, WE SHOULD BE RETURNING
        // return $this->postLog ABOVE INSTEAD TO GET A CORRECT RESPONSE
        return response()->json(['status' => 'success']);
    }

    /**
     * Add a new steps log.
     *
     * @param StepsActivityLogRequest $request
     * @param null $date
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postSteps(StepsActivityLogRequest $request, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        return $this->postLog($date, 'steps', $request->input('steps'));
    }

    /**
     * Add a new water log.
     *
     * @param WaterActivityLogRequest $request
     * @param null $date
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postWater(WaterActivityLogRequest $request, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        return $this->postLog(
            $date,
            'water',
            $request->input('glasses_of_water'),
            $request->getCalculationType()
        );
    }

    /**
     * Add a new weight log.
     *
     * @param WeightActivityLogRequest $request
     * @param $date
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postWeight(WeightActivityLogRequest $request, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $details = $request->input('details', []);
        return $this->postLog($date, 'weight', $request->input('weight'), null, $details);
    }

    /**
     * Add a new workout (group of exercises) log.
     *
     * @param WorkoutActivityLogRequest $request
     * @param null $date
     *
     * @return JsonResponse
     */
    public function postWorkout(WorkoutActivityLogRequest $request, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        try {
            $workoutId = $request->get('workout_id');

            if (!api_version() || !grhaft_enabled()) {
                $exercises = ExerciseCategory::find($workoutId)->exercises()->get();
            } else {
                if ($workoutId <= 0) {
                    // If Workout ID is equal or less than 0, a "Rest" workout has been submitted.
                    // "Rest" workouts are not a model in the database, they are more of a placeholder.
                    // In this case just create a generic exercise with an ID of 0 to complete, so that
                    // the user receives their daily exercise star.
                    $exercise = new Exercise();
                    $exercise->id = 0;
                    $exercises = [$exercise];
                } else {
                    $workout = Workout::findOrFail($workoutId);
                    if ($workout->workout_flow == Workout::FLOW_STANDARD) {
                        $exercises = $workout->load('rounds.roundExercises.exercise')
                            ->rounds
                            ->flatMap(fn ($round) => $round->roundExercises)
                            ->map(fn ($roundExercise) => $roundExercise->exercise)
                            ->unique('id');
                    } else {
                        $exercises = [$workout];
                    }
                }

                foreach ($exercises as $exercise) {
                    $this->postLog($date, 'exercise', $exercise->id, null);
                }
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: could not create workout log entry ' . $e->getMessage());
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove an existing weight log.
     *
     * @param WeightActivityLogRequest $request
     * @param string $date
     *
     * @return JsonResponse
     */
    public function deleteWeight(WeightActivityLogRequest $request, string $date)
    {
        return $this->deleteLog('weight', $date);
    }

    /**
     * Retrieve the steps log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function stepsLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('steps', $startDate, $endDate);
    }

    /**
     * Retrieve the water log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function waterLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('water', $startDate, $endDate);
    }

    /**
     * Retrieve the weight log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function weightLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('weight', $startDate, $endDate);
    }

    /**
     * Retrieve the calories log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function caloriesLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('calories', $startDate, $endDate);
    }

    /**
     * Retrieve the protein log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function proteinLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('protein', $startDate, $endDate);
    }

    /**
     * Retrieve the fat log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function fatLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('fat', $startDate, $endDate);
    }

    /**
     * Retrieve the fiber log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function fiberLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('fiber', $startDate, $endDate);
    }

    /**
     * Retrieve the carbohydrates log for the defined dates.
     *
     * @param Request $request
     * @param                          $startDate
     * @param                          $endDate
     *
     * @return JsonResponse
     */
    public function carbohydratesLog(Request $request, $startDate, $endDate)
    {
        // Map the dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        return $this->getLog('carbohydrates', $startDate, $endDate);
    }

    /**
     * Retrieve the averages log for a period of time.
     *
     * @param ActivityLogRequest $request
     * @param $category
     *
     * @return JsonResponse
     */
    public function averageLog(AverageLogRequest $request, $category)
    {
        $type = $request->input('type');
        // Period would consist of 'last-X-Y', where X is a numeric amount and Y is weeks/months
        $periodParts = explode('-', $request->input('period'));

        $toDate = now()->endOfDay();
        $fromDate = $this->getRangeStartDate($toDate, $periodParts[2], $periodParts[1]);

        $group = $type === 'week' ? 'YW' : 'Ym';
        $averages = $this->getActivitiesBetween($category, $fromDate, $toDate, function ($q) {
            $q->orderBy('date', 'asc');
        })
            ->filter(fn ($item) => $item->value != 0)
            ->groupBy(
                function ($item) use ($group) {
                    return Carbon::parse($item->date)->format($group);
                }
            )
            ->map(
                function ($item) use ($type) {
                    $avg = $item->avg('value');
                    $dates = $this->getDateRange($type, $item);
                    return [
                        'from' => $dates['start'],
                        'to' => $dates['end'],
                        'average' => number_format($avg, 1, '.', ''),
                    ];
                }
            )
            ->unique('from')
            ->values()
            ->toArray();

        return response()->json(['status' => 'success', 'data' => $averages]);
    }

    /**
     * Returns a totalWeightLoss for current and previous week
     *
     * @return JsonResponse
     */
    public function weightLossLog()
    {
        $category = 'weight';
        $latest = $this->latestLog($category);
        $date = now();
        $latestThisWeek = $this->weeklyAverage($date, $category);
        $userPreferences = auth('api')->user()->preferences;
        $startWeight = $userPreferences->start_weight;

        $previousWeek = is_null($latestThisWeek) ?
            0 :
            $this->weeklyAverage($date->subWeek(), $category);

        // Checks if record exists, returns latest Value, else their startWeight
        $latestWeight = !is_null($latest) && $latest->value > 0 ?
            (double) $latest->value :
            $startWeight;

        // If no latest weight, returns 0 else returns latestWeight - StartWeight
        $totalWeightLoss = is_null($latest) ? 0 : $latestWeight - $startWeight;
        // Checks if PreviousWeek and LatestThisWeek are null, returns 0 else latestThisWeek - previousWeek
        $lastWeekLoss = is_null($previousWeek) || is_null($latestThisWeek) ?
            0 :
            (double) $latestThisWeek - (double) $previousWeek;

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'total_weight_loss' => number_format($totalWeightLoss, 1, '.', ''),
                    'this_week_vs_last_week_loss' => number_format($lastWeekLoss, 1, '.', ''),
                    'type' => $category,
                ]
            ]
        );
    }


    /**
     * Get all activities between dates.
     * Optionally pass a query callback for additional filters.
     *
     * @var string $type
     * @var Carbon $from
     * @var Carbon $to
     * @var \Closure|null $queryCb
     */
    private function getActivitiesBetween(string $type, $from, $to, \Closure $queryCb = null)
    {
        $q = Activity::where('user_id', Auth::id())
            ->where('type', $type)
            ->whereBetween('date', [$from, $to]);

        if (!is_null($queryCb)) {
            $queryCb($q);
        }

        return $q->get();
    }

    /**
     * gets latest data for category passed in
     *
     * @param $category
     * @return mixed
     */
    public function latestLog($category)
    {
        $latest = Activity::where('user_id', Auth::id())
            ->where('type', $category)
            ->latest('date')
            ->first();
        return $latest;
    }

    /**
     * Returns the weekly average for passed in date/category
     *
     * @param $date
     * @param $category
     * @return mixed
     */
    public function weeklyAverage($date, $category)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $activities = $this->getActivitiesBetween($category, $startOfWeek, $endOfWeek);
        $activities = $activities->sortBy('date');  // Sorting is needed as retrieving weight logs sorts by date.

        $activities->filter(fn ($item) => $item->value != 0);

        return $activities->isEmpty() ? null : $activities->avg('value');
    }

    /**
     * User ID to return all periods for user
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getUserPeriods($id)
    {
        $period = ActivityNotes::where('user_id', $id)
            ->where('period', 'true')
            ->get();

        return ActivityNoteResource::collection($period);
    }


    /**
     * Delete a note
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteNote($id)
    {
        $note = ActivityNotes::findOrFail($id);
        $note->delete();
        return response()->noContent();
    }

    private function isCacheableActivity($type)
    {
        return ActivityCache::isCacheableActivity($type);
    }

    private function isStartAndEndOfSameWeek(Carbon $startDate, Carbon $endDate)
    {
        $start = $startDate->clone()->startOfDay();
        $end = $endDate->clone()->startOfDay();
        return $start->diffInDays($end) == 6 &&
            $start->isDayOfWeek(Carbon::MONDAY) && $end->isDayOfWeek(Carbon::SUNDAY);
    }

    /**
     * Return the start/end date for the current week
     *
     * @param $modifier
     * @param $year
     * @return array
     */
    private function getDatesForWeek($date)
    {
        $d = Carbon::parse($date);
        return [
            'start' => $d->startOfWeek()->format('Y-m-d'),
            'end' => $d->endOfWeek()->format('Y-m-d'),
        ];
    }

    /**
     * Return the start/end date for current month
     *
     * @param $modifier
     * @param $year
     * @return array
     */
    private function getDatesForMonth($date)
    {
        $d = Carbon::parse($date);
        return [
            'start' => $d->startOfMonth()->format('Y-m-d'),
            'end' => $d->endOfMonth()->format('Y-m-d'),
        ];
    }

    /**
     * Return an array of dates
     * start/end based on week/month
     *
     * @param string $range
     * @param $item
     * @return array
     */
    private function getDateRange(string $range, $item): array
    {
        if ($range === 'week') {
            $dates = $this->getDatesForWeek($item[0]->date);
        } else {
            $dates = $this->getDatesForMonth($item[0]->date);
        }
        return $dates;
    }

    /**
     * Get the start date of a date range, based on range type and value.
     *
     * @param \Carbon\Carbon $rangeEndDate
     * @param string $rangeType
     * @param int $rangeValue
     */
    private function getRangeStartDate($rangeEndDate, $rangeType, $rangeValue)
    {
        switch ($rangeType) {
            case 'weeks':
                return $rangeEndDate->copy()->subWeeks($rangeValue - 1)->startOfWeek();
            case 'months':
            default:
                return $rangeEndDate->copy()->subMonthsNoOverflow($rangeValue - 1)->startOfMonth();
        }
    }

    private function getCacheKey($type, Carbon $startDate, Carbon $endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = $startDate->clone();
        }
        return 'log:' . $type . ':' . auth('api')->user()->id
            . ':' . $startDate->format('Y-m-d')
            . ':' . $endDate->format('Y-m-d');
    }

    private function cache(string $key, $activities)
    {
        Redis::set($key, json_encode(collect($activities)), 'EX', 86400);
    }

    private function invalidateWeeklyCache(string $type, Carbon $date)
    {
        // Invalidate weekly and 7 day caches
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        Redis::del($this->getCacheKey($type, $startOfWeek, $endOfWeek));
    }
}
