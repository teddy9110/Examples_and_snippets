<?php

namespace Rhf\Modules\Activity\Services;

use Carbon\Carbon;
use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Exercise\Models\Exercise;
use Rhf\Modules\User\Models\User;

class ActivityService
{
    protected $query;
    protected $from;
    protected $to;
    protected $type;

    /**
     * Create a new UserService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->query = new Activity();
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Batch create logs for a given date for all types
     *
     * @param Carbon $date
     * @param array $data
     *
     * @return Activity[]
     */
    public function createLogs(Carbon $date, array $data, $mechanism = 'replace')
    {
        $result = [];

        $isSum = $mechanism == 'sum';
        $isReplaceOrSum = $isSum || $mechanism == 'replace';

        if ($isReplaceOrSum) {
            $existingActivities = Activity::where('user_id', '=', $this->getUser()->id)
                ->where('date', '=', $date)
                ->get();
        }

        foreach ($data as $type => $value) {
            if ($isReplaceOrSum) {
                $activity = $existingActivities
                    ->where('type', $type)
                    ->first();
            }

            if (!isset($activity)) {
                $activity = new Activity();
            }

            $activity->user_id = $this->getUser()->id;
            $activity->type = $type;
            $activity->value = $isSum && isset($activity) ? $activity->value + $value : $value;
            $activity->date = $date;
            $activity->save();

            $result[] = $activity;
        }

        return $result;
    }

    /**
     * Function createLog
     *
     * add a new activity log record
     *
     * @param string         $type          Type of log to create
     * @param Carbon $date          Date to add for
     * @param string         $value         Value to add
     * @param string         $mechanism     Update mechanism (sum, append, replace)
     *
     * @return Activity (object Activity)
     */
    public function createLog($type, Carbon $date, $value, $mechanism = 'replace')
    {
        return $this->createLogs($date, [ $type => $value ], $mechanism)[0];
    }

    /**
     * delete log for date
     * @param $type
     * @param Carbon $date
     * @return int
     */
    public function deleteLog($type, Carbon $date)
    {
        return Activity::where('user_id', '=', $this->getUser()->id)
            ->where('type', '=', $type)
            ->where('date', '=', $date)
            ->delete();
    }

    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the protected from variable
     *
     * @return Carbon object
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Return the query from the class.
     *
     * @return Query
     */
    public function getQuery()
    {
        if ($this->getUser()) {
            $this->query = $this->query->where('user_id', '=', $this->getUser()->id);
        }
        return $this->query;
    }

    /**
     * Return the protected to variable
     *
     * @return Carbon object
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Return the type associated to the instance of the service.
     *
     * @return void
     */
    public function getType()
    {
        return isset($this->type) ? $this->type : null;
    }

    /**
     * Return the user associated to the instance of the service.
     *
     * @return \Rhf\Modules\User\Models\User|null
     */
    public function getUser()
    {
        return isset($this->user) ? $this->user : null;
    }


    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/

    /**
     * Set the user associated to the instance of the service.
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }


    /**************************************************
    *
    * QUERY SCOPE
    *
    ***************************************************/

    /**
     * Filter the result set.
     *
     * @return self
     */
    public function filter()
    {
        if ($this->getUser()) {
            $this->query = $this->query->where('user_id', '=', $this->getUser()->id);
        }

        if ($this->getFrom() && $this->getTo() && $this->getFrom()->isSameDay($this->getTo())) {
            $this->query = $this->query->where('date', $this->getFrom());
        } else {
            if ($this->getFrom()) {
                $this->query = $this->query->where('date', '>=', $this->getFrom());
            }
            if ($this->getTo()) {
                $this->query = $this->query->where('date', '<=', $this->getTo());
            }
        }

        if ($this->getType()) {
            $this->query = $this->query->where('type', '=', $this->getType());
        }

        return $this;
    }

    /**
     * Order the result set.
     *
     * @return self
     */
    public function order()
    {
        $this->query = $this->query->orderBy('date', 'DESC');
        return $this;
    }

    /**
     * Return the query.
     *
     * @return object Query
     */
    public function retrieve()
    {
        $this->filter();
        if (!$this->getFrom() || !$this->getTo() || !$this->getFrom()->isSameDay($this->getTo())) {
            $this->order();
        }
        return $this->query;
    }

    /**
     * Reset the queyr ready to re-filter.
     *
     * @return self
     */
    public function resetQuery()
    {
        unset($this->dailyTotalSet);
        $this->query = new Activity();
        return $this;
    }

    /**
     * Group and total by daily count.
     *
     * @return self
     */
    public function dailyTotals()
    {
        // Check if this has previously been added
        if (isset($this->dailyTotalSet)) {
            return $this;
        }

        // Group by date and create sum
        $this->query = $this->query->groupBy('date', 'user_id', 'type')
            ->selectRaw('user_id, type, date, sum(`value`) as `value`');

        // We don't ever want to append this sql twice
        $this->dailyTotalSet = true;

        return $this;
    }

    /**
     * Get sum of values for activity type
     * between dates
     *
     * @param $type
     * @param $start
     * @param $end
     * @return mixed
     */
    public function weeklyTotals($type, $start, $end)
    {
        return $this->query->where('type', $type)
            ->where('user_id', $this->getUser()->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->sum('value');
    }

    public function generateUserActivityCsv(array $requestedActivities, Carbon $fromDate, Carbon $toDate)
    {
        $activityLog = Activity::select('user_id', 'type', 'value', 'date')
            ->where('user_id', $this->getUser()->id)
            ->whereIn('type', $requestedActivities)
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderBy('date', 'ASC')
            ->get();
        $filtered = $activityLog->where('type', 'exercise')->map(function ($item, $key) {
            return $item->value;
        });
        $exercises = Exercise::whereIn('id', $filtered)->get();

        $fileName = $this->getUser()->email . '-' . $fromDate . '-' . $toDate . '.csv';
        $pathname = storage_path('app') . '/' . $fileName;

        $fp = fopen($pathname, 'w');
        fputcsv($fp, array('user_id', 'user_name', 'type', 'value', 'date'));
        $user = User::find($this->getUser()->id);
        foreach ($activityLog as $logItem) {
            if ($logItem->type  == 'exercise') {
                if ($logItem->value == -1 || $logItem->value == 0) {
                    $logItem->value = 'Rest';
                }
                $logItem->value = $exercises->find($logItem->value)->title ?? 'Unknown';
            }
            $logItem->user_name = $user->first_name . ' ' . $user->surname;
            $logItemAttributes = $logItem->getAttributes();
            $logItems = [
                'user_id' => $logItemAttributes['user_id'],
                'user_name' => $logItemAttributes['user_name'],
                'type' => $logItemAttributes['type'],
                'value' => $logItemAttributes['value'],
                'date' => $logItemAttributes['date'],
            ];
            fputcsv($fp, $logItems);
        }
        fclose($fp);

        $fileParams = [];
        $fileParams['file_name'] = $fileName;
        $fileParams['file_path'] = $pathname;

        return $fileParams;
    }

    /**
     * Set type of log to retrieve.
     *
     * @param (string) type of activity
     * @return self
     */
    public function byType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set the from date for filtering the log query.
     *
     * @param Carbon $from
     *
     * @return self
     */
    public function from(Carbon $from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Retrieve organised collection as daily amounts with injected weekly average.
     *
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getDailyAndWeekly()
    {
        if (!$this->getType()) {
            throw new FitnessBadRequestException('Unable to retrieve daily and weekly activity logs.');
        }

        // Retrieve the data and structure for output
        if (!in_array($this->getType(), Activity::$replace)) {
            $this->dailyTotals();
        }

        // Get the data
        $logs = $this->retrieve()->get();

        // Loop and generate the weekly average
        $week = 7;
        $day = 1;
        $total = 0;
        $results = [];
        $previousRecordDay = false;
        foreach ($logs as $key => $log) {
            $results[] = $log;
            if ($day >= $week) {
                $results[] = [
                    'date'      => $log->date,
                    'type'      => $log->type,
                    'value'     => $total / $week,
                    'average'   => true,
                ];
                $total = 0;
                $day = 1;
            } else {
                // Check if we are in a new day
                if ($previousRecordDay != $log->date) {
                    $day = $day + $log->date->diffInDays($previousRecordDay);
                }
                $total = $total + $log->value;
            }

            // Set the previous record day to check when a day has passed
            $previousRecordDay = $log->date;
        }
        return collect($results);
    }

    /**
     * Set the from date for filtering the log query.
     *
     * @param Carbon $to
     *
     * @return self
     */
    public function to(Carbon $to)
    {
        $this->to = $to;
        return $this;
    }
}
