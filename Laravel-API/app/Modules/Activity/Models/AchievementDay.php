<?php

namespace Rhf\Modules\Activity\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Activity\Services\ProgressService;
use Rhf\Modules\User\Models\User;

class AchievementDay
{
    /** @var null|int */
    protected $totalStars;
    /** @var Collection */
    protected $activities;
    /** @var Collection */
    protected $weeklyActivities;
    /** @var User */
    protected $user;
    /** @var Carbon */
    protected $date;

    public function __construct(
        Carbon $date,
        User $user,
        Collection $activities = null,
        Collection $weeklyActivities = null
    ) {
        $this->date = $date;
        $this->activities = $activities;
        $this->weeklyActivities = $weeklyActivities;
        $this->user = $user;
    }

    /************************************
     *
     * Achievement retrieval methods
     *
     ************************************/

    /*
    * Function getMedal
    *
    * work out the medal for today
    *
    * @return (bool)
    */
    public function getMedal()
    {
        $stars = $this->getTotalStars();

        // Calculate medal
        $medal = 'Gold';
        if ($stars < 6) {
            $medal = 'Silver';
        }
        if ($stars < 4) {
            $medal = 'Bronze';
        }
        if ($stars < 3) {
            $medal = 'None';
        }

        return $medal;
    }

    /*
    * Function getTotalStars
    *
    * work out total number of stars
    *
    * @return (int)
    */
    public function getTotalStars()
    {
        // Check if already set
        if (isset($this->totalStars)) {
            return $this->totalStars;
        }

        $stars = 0;

        if ($this->hasCalorieStar()) {
            $stars++;
        }
        if ($this->hasExerciseStar()) {
            $stars++;
        }
        if ($this->hasFiberStar()) {
            $stars++;
        }
        if ($this->hasProteinStar()) {
            $stars++;
        }
        if ($this->hasStepsStar()) {
            $stars++;
        }
        if ($this->hasWaterStar()) {
            $stars++;
        }
        if ($this->hasWeightStar()) {
            $stars++;
        }

        $this->totalStars = $stars;

        return $stars;
    }


    /************************************
     *
     * Star retrieval methods
     *
     ************************************/

    /*
     * Function hasCalorieStar
     *
     * does the day have a star for calorie intake (if calorie intake is within 40 calories of goal)
     *
     * @return (bool)
     */
    public function hasCalorieStar()
    {
        try {
            $caloriesRecord = $this->activities->where('type', 'calories')->first();
            $caloriesGoal = $this->user->getPreference('daily_calorie_goal');
            if (
                $caloriesRecord && $caloriesGoal && $caloriesRecord->value < ($caloriesGoal + 40)
                && $caloriesRecord->value > ($caloriesGoal - 40)
            ) {
                return $this->calorieStar = true;
            }

            return $this->calorieStar = false;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve calorie star.');
        }
    }

    /*
     * Function hasExerciseStar
     *
     * does the day have a star for doing exercise (if they have viewed todays exercise OR if it's a rest day)
     *
     * @return (bool)
     */
    public function hasExerciseStar()
    {
        try {
            return $this->activities->where('type', 'exercise')->count() > 0;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve exercise star.');
        }
    }

    /*
     * Function hasFiberStar
     *
     * does the day have a star for fiber intake (if fiber intake is greater than goal)
     *
     * @return (bool)
     */
    public function hasFiberStar()
    {
        try {
            $fiberRecord = $this->activities->where('type', 'fiber')->first();
            $fiberGoal = $this->user->getPreference('daily_fiber_goal');

            if ($fiberRecord && $fiberGoal && $fiberRecord->value >= $fiberGoal) {
                return $this->fiberStar = true;
            }

            return $this->fiberStar = false;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve fibre star.');
        }
    }

    /*
     * Function hasProteinStar
     *
     * does the day have a star for protein intake (if protein intake is greater than goal)
     *
     * @return (bool)
     */
    public function hasProteinStar()
    {
        try {
            $proteinRecord = $this->activities->where('type', 'protein')->first();
            $proteinGoal = $this->user->getPreference('daily_protein_goal');

            if ($proteinRecord && $proteinGoal && $proteinRecord->value >= $proteinGoal) {
                return $this->proteinStar = true;
            }

            return $this->proteinStar = false;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve protein star.');
        }
    }

    /*
     * Function hasStepsStar
     *
     * does the day have a star for steps
     *
     * @return (bool)
     */
    public function hasStepsStar()
    {
        try {
            $stepGoal = $this->user->getPreference('daily_step_goal');

            if (api_version() < 20210201) {
                $stepActivity = $this->activities->where('type', 'steps')->first();
                return !is_null($stepActivity) ? $stepActivity->value >= $stepGoal : false;
            }

            $progressService = new ProgressService();
            $progressService->setUser(auth()->user())->from($this->date);
            $weeklyTarget = $progressService->getWeeklyTarget($this->date, $stepGoal);

            if (!is_null($this->weeklyActivities)) {
                $weeklyProgressionData = $this->weeklyActivities
                    ->where('type', 'steps')
                    ->where('date', '<=', $this->date);
            } else {
                $weeklyProgressionData = $progressService
                    ->getWeeklyProgression('steps', $this->date);
            }

            $target = $progressService->getAverageRemainingSteps(
                $stepGoal,
                $weeklyTarget,
                $weeklyProgressionData
            );

            $stepsForDay = $weeklyProgressionData->where('date', $this->date)->first();

            return !is_null($stepsForDay) ? $stepsForDay->value >= $target : false;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve steps star.');
        }
    }

    /*
     * Function hasWaterStar
     *
     * does the day have a star for water
     *
     * @return (bool)
     */
    public function hasWaterStar()
    {
        try {
            $waterGoal = $this->user->getPreference('daily_water_goal');
            return ((int) $this->activities->where('type', 'water')->sum('value')) >= $waterGoal;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve water star.');
        }
    }

    /*
     * Function hasWeightStar
     *
     * does the day have a star for weight
     *
     * @return (bool)
     */
    public function hasWeightStar()
    {
        try {
            return $this->activities->where('type', 'weight')->count() > 0;
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve weight star.');
        }
    }


    /************************************
     *
     * Getters
     *
     ************************************/

    public function getDate()
    {
        return $this->date;
    }
}
