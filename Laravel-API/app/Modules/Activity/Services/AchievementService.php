<?php

namespace Rhf\Modules\Activity\Services;

use Carbon\Carbon;
use Rhf\Modules\Activity\Models\AchievementDay;
use Rhf\Modules\User\Models\User;

class AchievementService
{
    protected $from; // Set the from date

    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the from date.
     *
     * @return Carbon date
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Return the user associated to the instance of the service.
     *
     * @return \Rhf\Modules\User\Models\User
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
     * @param \Rhf\Modules\User\Models\User $user
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
     * Set the from date for filtering the log query.
     *
     * @param \Carbon\Carbon $date
     *
     * @return self
     */
    public function from(Carbon $date)
    {
        $this->from = $date;
        return $this;
    }

    /**
     * Get daily medals for a given date range and period.
     * Period can be - 'week' or 'month'.
     *
     * @param \DatePeriod $range Date range
     * @param string $period Date period - 'week' or 'month'
     * @param string $sortDirection Sort direction - 'asc' (default) or 'desc'
     *
     * @return array
     */
    public function getMedalsForRange(\DatePeriod $range, string $period, string $sortDirection = 'asc'): array
    {
        $user = auth('api')->user();
        $activityService = (new ActivityService())->setUser($user);

        $activities = $activityService->from(Carbon::parse($range->start))
            ->to(Carbon::parse($range->end))
            ->retrieve()
            ->get();

        $medals = collect($range)->map(function ($week) use ($activities, $period, $sortDirection, $user) {
            $d = Carbon::parse($week);
            $startDate = $d->copy()->startOf($period);
            $endDate = $d->copy()->endOf($period);

            $periodActivities = $activities->whereBetween('date', [
                $startDate,
                $endDate
            ]);

            $periodDailyRange = new \DatePeriod(
                new \DateTime($startDate),
                new \DateInterval('P1D'),
                new \DateTime($endDate)
            );

            $days = [];
            foreach ($periodDailyRange as $date) {
                $date = Carbon::parse($date);
                $dailyActivities = $periodActivities->where('date', $date);
                $achDay = new AchievementDay(
                    $date,
                    $user,
                    $dailyActivities,
                    $periodActivities
                );
                $days[] = [
                    'date' => $date->format('Y-m-d\TH:i:sO\Z'),
                    'medal' => $achDay->getMedal() === 'None' ? null : $achDay->getMedal(),
                ];
            }
            return [
                $period . '_beginning' => $startDate->format('Y-m-d\TH:i:sO\Z'),
                'awards' => $sortDirection == 'asc' ? $days : array_reverse($days),
            ];
        });

        return $sortDirection == 'asc' ? $medals->toArray() : array_reverse($medals->toArray());
    }
}
