<?php

namespace Rhf\Modules\Activity\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\User\Models\User;

class AchievementWeek
{
    /** @var Collection */
    protected $activities;
    /** @var Collection */
    protected $weeklyActivities;
    /** @var Carbon */
    protected $date;
    /** @var User */
    protected $user;


    public function __construct(
        Carbon $date,
        User $user,
        Collection $activities = null
    ) {
        $this->date = $date;
        $this->user = $user;
        $this->activities = $activities;
    }

    /*
    * Function getMedal
    *
    * work out the medal for today
    *
    * @return (bool)
    */
    public function getWeeklyMedal()
    {
        $medals = [
            'Gold' => 0,
            'Silver' => 0,
            'Bronze' => 0,
            'None' => 0,
        ];

        // Get the dates and activities in range
        $date = $this->date;

        // Loop days by modifying dependent activityService
        $i = 0;
        while ($i < 7) {
            // Generate a new day
            $rangedActivities = $this->activities->where('date', $date);
            $achievementDay = new AchievementDay($date, $this->user, $rangedActivities, $this->activities);

            // Get medal and increment
            $medals[$achievementDay->getMedal()]++;
            $date = $date->addDay();
            $i++;
        }

        if ($medals['Gold'] > 5) {
            return 'Gold';
        }
        if ($medals['Silver'] > 3) {
            return 'Silver';
        }
        if ($medals['Bronze'] > 2) {
            return 'Bronze';
        }
        return 'None';
    }

    /*
    * Function getWeeklyStarsByMetric
    *
    * work out the total stars per metric for the week
    *
    * @return (bool)
    */
    public function getWeeklyStarsByMetric()
    {
        $stars = [
            'calories' => 0,
            'exercise' => 0,
            'fiber' => 0,
            'protein' => 0,
            'steps' => 0,
            'water' => 0,
            'weight' => 0,
        ];

        // Get the dates and activities in range
        $date = $this->date;

        // Loop days by modifying dependent activityService
        $i = 0;
        while ($i < 7) {
            // Assign this to an achievement day
            $rangedActivities = $this->activities->where('date', $date);
            $achievementDay = new AchievementDay($date, $this->user, $rangedActivities, $this->activities);

            if ($achievementDay->hasCalorieStar()) {
                $stars['calories']++;
            }
            if ($achievementDay->hasExerciseStar()) {
                $stars['exercise']++;
            }
            if ($achievementDay->hasFiberStar()) {
                $stars['fiber']++;
            }
            if ($achievementDay->hasProteinStar()) {
                $stars['protein']++;
            }
            if ($achievementDay->hasStepsStar()) {
                $stars['steps']++;
            }
            if ($achievementDay->hasWaterStar()) {
                $stars['water']++;
            }
            if ($achievementDay->hasWeightStar()) {
                $stars['weight']++;
            }

            $date = $date->addDay();
            $i++;
        }

        return $stars;
    }
}
