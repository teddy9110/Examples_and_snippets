<?php

namespace Rhf\Modules\Admin\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\User\Models\UserPreferences;

class AdminMockService
{
    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param $target
     * @param $type
     * @param $id
     */
    public function createFactoryActivity(Carbon $startDate, Carbon $endDate, $type, $id)
    {
        $range = get_period_range($startDate, $endDate, 'day');
        foreach ($range as $date) {
            $value = $this->getMedalValue($id, $type);
            $this->createMockActivity($type, $id, $value, $date);
        }
    }

    /**
     * @param $type
     * @param $id
     * @param int $value
     * @param DateTimeInterface $date
     */
    public function createMockActivity($type, $id, int $value, DateTimeInterface $date): void
    {
        Activity::factory()
            ->modifier($type, $value, $value)
            ->create([
                'user_id' => $id,
                'date' => $date->format('Y-m-d')
            ]);
    }

    /**
     * @param $id
     * @param $activity
     * @return int
     */
    public function getMedalValue($id, $activity)
    {
        $userPreferences = UserPreferences::where('user_id', $id)->first();
        if ($activity === 'calories' || $activity === 'steps') {
            $activity = rtrim($activity, 's');
        }

        if ($activity === 'exercise') {
            return 0;
        }

        $preference = "daily_{$activity}_goal";
        return $userPreferences->{$preference};
    }

    public function getStarsForMedal($medal)
    {
        switch ($medal) {
            case 'bronze':
                return 3;
            case 'silver':
                return 5;
            case 'gold':
                return 7;
            default:
                return 3;
        }
    }
}
