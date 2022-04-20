<?php

namespace Rhf\Modules\Activity\Services;

use Carbon\Carbon;
use DatePeriod;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\Activity\Filters\ActivityFilter;
use Rhf\Modules\Activity\Models\Activity;

class ActivitiesService
{
    /**
     * @param $data
     * @return mixed
     */
    public function createActivity($data)
    {
        return Activity::updateOrCreate(
            [
                'type' => $data['type'],
                'date' => $data['date'],
                'user_id' => $data['user_id'],
            ],
            ['value' => $data['value']]
        );
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getActivity($id)
    {
        return Activity::findOrFail($id);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getActivities(ActivityFilter $filters, array $pagination = [])
    {
        $activity = Activity::where('user_id', Auth::id())
            ->with('notes')
            ->filter($filters);
        if (empty($pagination)) {
            return $activity->get();
        }
        return $activity->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    /**
     * @param Activity $activity
     * @param $data
     * @return Activity
     */
    public function updateActivity(Activity $activity, $data): Activity
    {
        $activity->update([
            'value' => $data['value'],
            'date' => $data['date']
        ]);
        return $activity;
    }


    /**
     * @param string $type
     * @param $date
     * @return mixed
     */
    public function getActivityByTypeAndDate(string $type, $date)
    {
        return Activity::where('user_id', auth('api')->user()->id)
            ->where('type', $type)
            ->where('date', $date)
            ->first();
    }

    /**
     * Return an average based on passed in dates
     *
     * @param Collection $activity
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    public function getAveragesBetweenDates(Collection $activity, Carbon $startDate, Carbon $endDate)
    {
        $avg = $activity->whereBetween('date', [$endDate, $startDate])->avg('value');
        return (double) number_format($avg, 2);
    }

    /**
     * @param Activity $activity
     * @return bool|null
     * @throws Exception
     */
    public function deleteActivity(Activity $activity): ?bool
    {
        return $activity->delete();
    }

    /**
     * @param Collection $activity
     * @param DatePeriod $range
     * @param string $period
     * @param string $sortDirection
     * @return Collection
     */
    public function activitiesGroupByDate(
        Collection $activity,
        DatePeriod $range,
        string $period,
        string $sortDirection
    ): Collection {
        $groupBy = $period === 'week' ? 'Y-W' : 'Y-m';

        $weeks = [];
        foreach ($range as $dates) {
            $date = Carbon::parse($dates);
            $weeks[] = [
                'start_date' => $date->copy()->startOf($period),
                'end_date' => $date->copy()->endOf($period),
            ];
        }

        $activities = $activity
            ->filter(function ($item) {
                return $item->value != 0;
            })
            ->groupBy(function ($item) use ($groupBy) {
                return Carbon::parse($item->date)->format($groupBy);
            })
            ->values();

        $groups = collect($weeks)
            ->map(function ($week) use ($activities) {
                $startDate = $week['start_date'];
                $endDate = $week['end_date'];
                return [
                    'period_start' => iso_date($week['start_date']),
                    'activity' => $this->formatGroupedActivities(
                        $activities->flatten()->whereBetween('date', [
                            $startDate,
                            $endDate
                        ])
                    ),
                ];
            });

        if ($sortDirection === 'desc') {
            return $groups->sortByDesc(function ($items) {
                return $items['period_start'];
            });
        }

        return $groups;
    }

    private function formatGroupedActivities(Collection $activities)
    {
        return $activities->map(function ($item) {
            $return = [
                'activity_id' => $item->id,
                'type' => $item->type,
                'value' => $item->value,
                'date' => iso_date($item->date),
                'friendly_date' => iso_date($item->date),
            ];

            if ($item->type === 'weight') {
                $return['details'] = [
                    'note' => is_null($item->notes) ? null : $item->notes->note,
                    'period' => is_null($item->notes) ? 'unknown' : $item->notes->period,
                ];
            }

            return $return;
        })->values();
    }
}
