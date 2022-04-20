<?php

namespace Rhf\Modules\Admin\Services;

use Rhf\Modules\User\Models\User;

class AdminManagementService
{
    /**
     * returns a count of all customers
     *
     * @return int
     */
    public function totalUsersCount(): int
    {
        return User::customer()->count();
    }

    /**
     * Return a percentage of user change for year
     *
     * @return float
     */
    public function totalNetUsers(): float
    {
        $start = $this->totalActiveUserCount();
        $end = $start - $this->totalUserExpirations();
        return $this->calculateChurnRate($start, $end);
    }

    public function totalUserExpirations()
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'expiry_date',
                [
                    User::first()->value('created_at'),
                    now()->endOfDay()
                ]
            )->count();
    }

    /**
     *
     * returns a count for all users in system
     *
     * @return int
     */
    public function totalActiveUserCount(): int
    {
        return User::customer()
            ->wherePaid(1)
            ->whereActive(1)
            ->where('expiry_date', '>=', now())
            ->count();
    }

    /**
     * Return a count of new users for previous month
     *
     * @return int
     */
    public function newUsersCurrentMonth(): int
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'created_at',
                [
                    $this->firstDayOfMonth(),
                    $this->lastDayOfMonth()
                ]
            )->count();
    }

    /**
     * returns a value of active users / expiring users for this month
     *
     * @return float
     */
    public function netUsersCurrentMonth(): float
    {
        $startOfMonth = $this->totalActiveUserCount();
        $endOfMonth = $this->totalActiveUserCount() - $this->expiredUserCurrentMonth();
        return $this->calculateChurnRate($startOfMonth, $endOfMonth);
    }

    /**
     * Return a count of users expiring this month
     *
     * @return int
     */
    public function expiredUserCurrentMonth(): int
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'expiry_date',
                [
                    $this->firstDayOfMonth(),
                    $this->lastDayOfMonth()
                ]
            )
            ->count();
    }

    /**
     *
     * Return the count of users where they expire withing N days of today
     *
     * @param $day
     * @return int
     */
    public function membersExpiringInNDays($day): int
    {
        return User::customer()
            ->wherePaid(1)
            ->whereBetween(
                'expiry_date',
                [
                    now()->startOfDay(),
                    date('Y-m-d H:i:s', strtotime("+$day days"))
                ]
            )->count();
    }

    /**
     *
     * Return a count of the users workout location
     *
     * @param $type
     * @return int
     */
    public function workoutType($type)
    {
        return User::activePaidCustomer()
            ->whereHas(
                'preferences',
                function ($query) use ($type) {
                    if ($type === 'gym') {
                        // GYM USER
                        $query->where('exercise_location_id', 1);
                    } elseif ($type === 'home') {
                        // HOME USER | Standard
                        $query->where('exercise_location_id', 2)
                            ->where('exercise_level_id', 2);
                    } else {
                        // HOME USER | Athletic | GRHAFT
                        $query->where('exercise_location_id', 2)
                            ->where('exercise_level_id', 1);
                    }
                }
            )
            ->count();
    }

    /**
     * Return a count of users who have been created during given period
     * Query is looking at the previous date to current date
     * eg: 2020-11-18 to 2020-11-25
     * @param $period
     * @return int
     */
    public function getNewUserCountDuringPeriod($from, $to): int
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'created_at',
                [
                    $from,
                    $to
                ]
            )
            ->count();
    }

    /**
     * Return a count of users who are expiring during the period given
     *
     * @param $period
     * @return int
     */
    public function getExpiringUserCountDuringPeriod($from, $to): int
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'expiry_date',
                [
                    $from,
                    $to
                ]
            )
            ->count();
    }

    /**
     * Return a count of users within the current month
     *
     * @return int
     */
    private function activeUserCurrentMonth(): int
    {
        return User::activePaidCustomer()
            ->whereBetween(
                'created_at',
                [
                    $this->firstDayOfMonth(),
                    $this->lastDayOfMonth()
                ]
            )->count();
    }

    /**
     * Returns first day of the month with timestamp 00:00:00
     *
     * @return false|string
     */
    private function firstDayOfMonth()
    {
        return date('Y-m-d H:i:s', strtotime('first day of this month'));
    }

    /**
     * Returns Last day of the month with timestamp 23:59:59
     *
     * @return false|string
     */
    private function lastDayOfMonth()
    {
        return date('Y-m-d H:i:s', strtotime('last day of this month'));
    }

    /**
     * Calculate userChurn rate
     *
     * @param int $start
     * @param $end
     * @return int|string
     */
    private function calculateChurnRate(int $start, $end)
    {
        if ($start == 0) {
            return 0;
        }
        return number_format(($start - $end) / $start, 2);
    }
}
