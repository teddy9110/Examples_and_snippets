<?php

namespace Rhf\Modules\Activity\Services;

use Carbon\Carbon;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Activity\Models\Activity;

class ProgressService extends ActivityService
{
    protected $types = ['Calories', 'Protein', 'Steps', 'Water', 'Fiber', 'Exercise', 'Weight'];

    /**************************************************
     *
     * PUBLIC METHODS
     *
     ***************************************************/

    /*
     * Function dailyProgress
     *
     * get daily progress record
     *
     * @return (array)
     */
    public function dailyProgress()
    {
        $progress = [];
        $activityData = $this->retrieve()->get();
        foreach ($this->types as $type) {
            $method = 'daily' . $type . 'Progress';
            $this->resetQuery();
            $progress[$type] = $this->{$method}($activityData);
        }

        return $progress;
    }

    /*
     * Function dailyCaloriesProgress
     *
     * get a calorie progress record
     *
     * @return (array)
     */
    public function dailyCaloriesProgress($data = null)
    {
        if (!is_null($data)) {
            $records = $data->where('type', 'calories')->sortByDesc('updated_at');
            $record = $records->first();
        } else {
            $this->byType('calories');
            // Get the record and target for comparison
            $records = $this->retrieve()->get()->sortByDesc('updated_at');
            $record = $records->first();
        }
        $target = $this->getUser()->getPreference('daily_calorie_goal');

        if (!$target) {
            throw new FitnessBadRequestException('Unable to retrieve calorie progress without calorie goal set.');
        }

        $calorieMargin = 40; //Value from FE easily changeable
        $min = ($target - $calorieMargin); //minVariance
        $max = ($target + $calorieMargin); //maxVariance

        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'target' => (int) $target,
            'progress' => $record ? (float) $record->value : 0,
            'daily_target_met' => $record ? $this->isWithinRange((int) $record->value, $min, $max) : false
        ];
    }

    /*
     * Function dailyExerciseProgress
     *
     * get an exercise progress record
     *
     * @return (array)
     */
    public function dailyExerciseProgress($data = null)
    {
        if (!is_null($data)) {
            $record = $data->where('type', 'exercise')->first();
        } else {
            $this->byType('exercise');
            // Get the record and target for comparison
            $record = $this->retrieve()->first();
        }


        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'progress' => $record ? 1 : 0,
            'daily_target_met' => $record ? true : false
        ];
    }

    /*
     * Function dailyFiberProgress
     *
     * get a fiber progress record
     *
     * @return (array)
     */
    public function dailyFiberProgress($data = null)
    {
        if (!is_null($data)) {
            $records = $data->where('type', 'fiber')->sortByDesc('updated_at');
            $record = $records->first();
        } else {
            $this->byType('fiber');
            // Get the record and target for comparison
            $records = $this->retrieve()->get()->sortByDesc('updated_at');
            $record = $records->first();
        }
        $target = $this->getUser()->getPreference('daily_fiber_goal');

        if (!$target) {
            throw new FitnessBadRequestException('Unable to retrieve fiber progress without fiber goal set.');
        }

        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'target' => (int) $target,
            'progress' => $record ? (float) $record->value : 0,
            'daily_target_met' => $this->isTargetMet($record, $target)
        ];
    }

    /*
     * Function dailyProteinProgress
     *
     * get a protein progress record
     *
     * @return (array)
     */
    public function dailyProteinProgress($data = null)
    {
        if (!is_null($data)) {
            $records = $data->where('type', 'protein')->sortByDesc('updated_at');
            $record = $records->first();
        } else {
            $this->byType('protein');
            // Get the record and target for comparison
            $records = $this->retrieve()->get()->sortByDesc('updated_at');
            $record = $records->first();
        }
        $target = $this->getUser()->getPreference('daily_protein_goal');

        if (!$target) {
            throw new FitnessBadRequestException('Unable to retrieve protein progress without protein goal set.');
        }

        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'target' => (int) $target,
            'progress' => $record ? (float) $record->value : 0,
            'daily_target_met' => $this->isTargetMet($record, $target)
        ];
    }

    /*
     * Function dailyStepsProgress
     *
     * get a step progress record
     *
     * @return (array)
     */
    public function dailyStepsProgress($data = null)
    {
        if (!is_null($data)) {
            $record = $data->where('type', 'steps')->first();
        } else {
            $this->byType('steps');
            $record = $this->retrieve()->first();
        }
        $target = $this->getUser()->getPreference('daily_step_goal');

        if (!$target) {
            throw new FitnessBadRequestException('Unable to retrieve steps progress without step goal set.');
        }

        if (api_version() >= 20210201) {
            $weeklyTarget = $this->getWeeklyTarget($this->getFrom()->copy(), $target);

            $weeklyProgressionData = $this->getWeeklyProgression('steps', $this->getFrom());
            $weeklyProgress = $weeklyProgressionData->sum('value');
            $target = $this->getAverageRemainingSteps($target, $weeklyTarget, $weeklyProgressionData);

            return [
                'date' => $this->getFrom()->format('Y-m-d'),
                'target' => (int) $target,
                'progress' => $record ? (float) $record->value : 0,
                'weekly_progress' => $weeklyProgress,
                'weekly_target' => $weeklyTarget,
                'daily_target_met' => $target == 0 ? true : $this->isTargetMet($record, $target),
                'weekly_target_met' => $weeklyProgress >= $weeklyTarget ? true : false,
            ];
        } else {
            return [
                'date' => $this->getFrom()->format('Y-m-d'),
                'target' => (int) $target,
                'progress' => $record ? (float) $record->value : 0,
                'daily_target_met' => $this->isTargetMet($record, $target),
            ];
        }
    }

    /**
     * Get the current weekly progression from start of week to current day -1
     * checks if sunday and returns normal date on a sunday
     *
     * @param $type
     * @param $date
     * @return mixed
     */
    public function getWeeklyProgression($type, $date)
    {
        $c = Carbon::parse($date);
        return $this->getQuery()
            ->where('type', $type)
            ->whereBetween('date', [$c->copy()->startOfWeek(), $c->copy()])
            ->get();
    }

    /*
     * Function dailyWaterProgress
     *
     * get a water progress record
     *
     * @return (array)
     */
    public function dailyWaterProgress($data = null)
    {
        if (!is_null($data)) {
            $value = $data->where('type', 'water')->sum('value');
        } else {
            $this->byType('water');
            // Get the record and target for comparison
            $record = $this->dailyTotals()->retrieve()->first();
            $value = $record ? (float) $record->value : 0;
        }
        $target = $this->getUser()->getPreference('daily_water_goal');

        if (!$target) {
            throw new FitnessBadRequestException('Unable to retrieve water progress without water goal set.');
        }

        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'target' => (int) $target,
            'progress' => $value,
            'daily_target_met' => $value >= $target ? true : false,
        ];
    }

    /*
     * Function dailyWeightProgress
     *
     * get a weight progress record
     *
     * @return (array)
     */
    public function dailyWeightProgress($data = null)
    {
        if (!is_null($data)) {
            $record = $data->where('type', 'weight')->first();
        } else {
            $this->byType('weight');

            $record = Activity::where('user_id', $this->getUser()->id)
                ->where('type', 'weight')
                ->where('date', $this->getFrom()->copy())->first();
        }

        $lastSevenDaysRecords = Activity::where('user_id', $this->getUser()->id)
            ->where('type', 'weight')
            ->whereBetween('date', [$this->getFrom()->copy()->subDays(7), $this->getTo()])
            ->get();

        if (count($lastSevenDaysRecords) == 0) {
            $sevenDayAverage = null;
        } else {
            $sevenDayAverage = 0;

            foreach ($lastSevenDaysRecords as $item) {
                $sevenDayAverage += $item->value;
            }

            $sevenDayAverage = $sevenDayAverage / count($lastSevenDaysRecords);
        }
        return [
            'date' => $this->getFrom()->format('Y-m-d'),
            'target' => $sevenDayAverage,
            'progress' => $record ? (float)$record->value : null,
            'daily_target_met' => !is_null($record)
        ];
    }

    /**
     * Return an average of steps based on Target
     * Gets all values in DB for the week and sums them
     * Removes sum from total target and divides by number of days left in week based on date
     *
     * @param $target
     * @return int
     */
    public function getAverageRemainingSteps($target, $weeklyTarget, $weeklyProgressionData)
    {
        $date = Carbon::parse($this->getFrom());
        $startOfWeek = $date->copy()->startOfWeek();

        if ($startOfWeek == $date) {
            return $target;
        }
        $daysRemaining = $this->daysLeftInWeek();

        $weeklyStepsTotal = $weeklyProgressionData->filter(function ($item) use ($date) {
            return !$date->isSameDay($item->date);
        })->sum('value');

        if ($daysRemaining > 0) {
            $value = intval(round((($weeklyTarget - $weeklyStepsTotal) / $daysRemaining)));
            return $value > 0 ? $value : 0;
        } else {
            $value = intval(($weeklyTarget - $weeklyStepsTotal));
            return $value > 0 ? $value : 0;
        }
    }

    /**
     * Return a boolean if the user has met the target
     *
     * @param $record
     * @param $target
     * @return bool
     */
    private function isTargetMet($record, $target): bool
    {
        return !is_null($record) ? round($record->value) >= (int) $target : false;
    }

    /**
     * Return Bool if the calorie count is within range
     *
     * @param $value
     * @param $min
     * @param $max
     * @return bool
     */
    private function isWithinRange($value, $min, $max): bool
    {
        return false ? ($value >= $min && $value <= $max) : ($value >= $min && $value <= $max);
    }

    /**
     * Checks if the user was created this week
     *
     * @return bool
     */
    private function userCreatedThisWeek(): bool
    {
        $date = Carbon::parse($this->getFrom());

        return $this->getUser()->created_at->between(
            $date->copy()->startOfWeek(),
            $date->copy()->endOfWeek()
        );
    }

    /**
     * @return int
     */
    private function daysLeftInWeek(): int
    {
        $date = Carbon::parse($this->getFrom());
        $endOfWeek = $date->copy()->endOfWeek();
        $daysRemaining = $date->copy()->subDay()->diffInDays($endOfWeek);
        return $daysRemaining;
    }

    /**
     * @param Carbon $medalDate
     * @param $stepGoal
     * @return float|int
     */
    public function getWeeklyTarget(Carbon $medalDate, $stepGoal)
    {
        $user = auth()->user();
        $newUser = $user->created_at->between(
            $medalDate->copy()->startOfWeek(),
            $medalDate->copy()->endOfWeek()
        );
        if ($newUser) {
            $createdAt = Carbon::parse($user->created_at)->startOfDay();
            $totalDaysToCalculate = $medalDate->copy()->endOfWeek()->floatDiffInDays($createdAt);
            $totalDaysToCalculate = (int)ceil($totalDaysToCalculate);
            $weeklyTarget = $stepGoal * $totalDaysToCalculate;
        } else {
            $weeklyTarget = $stepGoal * 7;
        }
        return $weeklyTarget;
    }
}
