<?php

namespace Rhf\Modules\MyFitnessPal\Services;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\MyFitnessPal\Models\Meal;
use Rhf\Modules\MyFitnessPal\Models\Day;
use Rhf\Modules\System\Models\ActivityLog;
use Rhf\Modules\User\Models\User;

class DiaryService extends MyFitnessPalService
{
    protected $route = 'diary';
    protected $fields = [
        'nutritional_contents'
    ];
    protected $collection;


    /**************************************************
     *
     * PUBLIC METHODS
     *
     **************************************************
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param User|null $user
     */

    /*
     * Function sync
     *
     * sync data for the provided date with MFP
     *
     * @param (Carbon) date
     * @return (void)
     */
    public static function sync(Carbon $startDate, Carbon $endDate = null, $user = null)
    {
        if (!$endDate) {
            $endDate = clone $startDate;
        }

        $diaryService = new DiaryService($user);
        $diaryUser = $diaryService->getUser();

        $syncTokens = [];
        for ($date = (clone $startDate); $date->lte($endDate); $date->addDay()) {
            $dateTimestamp = (clone $date)->startOfDay()->timestamp;
            $redisId = "mfp-sync:$diaryUser->id:$dateTimestamp";
            $token = uniqid();

            $syncTokens["$dateTimestamp"] = $token;

            if (!Redis::get($redisId)) {
                Redis::set($redisId, $token, 'EX', 300); // lock this date from syncing for 5 mins
            }
        }

        // sleep for random time between 20 - 100ms to prevent sync collisions
        usleep(rand(20, 100) * 1000);

        // if mfp token expired, unlink mfp
        if ($diaryUser && $diaryUser->hasConnectedMfp() && $diaryUser->hasMfpExpired()) {
            try {
                $mfp = new MyFitnessPalService($diaryUser);
                $mfp->refreshToken();

                $log = new ActivityLog();
                $log->user_id = $diaryUser->id;
                $log->action = 'MyFitnessPalRefreshed';
                $log->save();
            } catch (\Exception $e) {
                $diaryUser->unlinkMfp();

                $log = new ActivityLog();
                $log->user_id = $diaryUser->id;
                $log->action = 'MyFitnessPalExpired';
                $log->save();
            }

            // don't proceed with sync, if we have unlinked user
            if (!$diaryUser->hasConnectedMfp()) {
                return;
            }
        }

        try {
            for ($date = (clone $startDate); $date->lte($endDate); $date->addDay()) {
                $dateTimestamp = (clone $date)->startOfDay()->timestamp;
                $token = $syncTokens["$dateTimestamp"];
                $redisKey = "mfp-sync:$diaryUser->id:$dateTimestamp";
                $redisToken = Redis::get($redisKey);

                // Did we lock this date for sync?
                if (!$redisToken || $token != $redisToken) {
                    continue;
                }

                $diaryService->setDate($date);
                $diaryService->collection();
                $diaryService->log();

                Redis::del($redisKey);
            }
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: Unable to sync MyFitnessPal data. Please contact Team RH Support'
            );
        }
    }

    /*
     * Function single
     *
     * retrieve a single record
     *
     * @param (ID) ID to retrieve
     * @return (array)
     */
    public function single()
    {
    }

    /*
     * Function collection
     *
     * retrieve a collection of records
     *
     * @param (ID) ID to retrieve
     * @return (array)
     */
    public function collection()
    {
        $data = [
            'fields' => $this->fields,
            'entry_date' => $this->getDate()->format('Y-m-d'),
            'max_items' => 100,
        ];

        $redisId = $this->redisKey('mfp:collection:' . $this->route . ':' . $this->getUser()->id, $data);

        // Check redis store
        // User ID 29719 is Matt Wade - no clue why is this excluded.
        if (Redis::get($redisId) && auth('api')->user()->id != 29719) {
            $items = json_decode(Redis::get($redisId));
            $recache = false;
        } else {
            $res = $this->request('GET', $this->route, $data);
            $items = json_decode($res->getBody())->items;
            $recache = true;
        }

        // Create individual meals
        $meals = [];
        foreach ($items as $meal) {
            // Only return results that have meals
            if ($meal->type == 'diary_meal') {
                $meals[] = new Meal($meal);
            }
        }

        // Set Redis store
        if ($recache) {
            Redis::set($redisId, json_encode($items), 'EX', 600);
        }

        return $this->collection = collect($meals);
    }

    public function day()
    {
        // Create diary day
        return new Day($this->collection);
    }

    /*
     * Function log
     *
     * log the data using activity logger
     *
     * @return (self)
     */
    public function log()
    {
        $activityService = new ActivityService();
        $activityService->setUser($this->user);

        $activityService->createLogs($this->getDate(), [
            'calories' => $this->day()->getCalories(),
            'protein' => $this->day()->getProtein(),
            'fat' => $this->day()->getFat(),
            'fiber' => $this->day()->getFiber(),
            'carbohydrates' => $this->day()->getCarbohydrates(),
        ], 'replace');

        return $this;
    }


    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/
    public function getDate()
    {
        if (isset($this->date)) {
            return $this->date;
        }
        return Carbon::now();
    }

    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/
    public function setDate($date)
    {
        $date = Carbon::parse($date);

        $this->date = $date;
        return $this;
    }
}
