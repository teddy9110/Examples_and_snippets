<?php

namespace Rhf\Modules\User\Services;

use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\System\Services\CsvService;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;

class TargetService
{
    protected $daily_protein_goal;
    protected $daily_fat_goal;
    protected $daily_fiber_goal;
    protected $daily_carb_goal;

    /**
     * Create a new TargetService instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Do we have enough provided data to calculate the user's goals
     *
     * @return bool
     */
    public function canCalculateGoals($data)
    {
        if (
            (isset($data['start_weight']) || $this->getUser()->hasPreference('start_weight'))
            && (isset($data['daily_step_goal']) || $this->getUser()->hasPreference('daily_step_goal'))
            && (isset($data['gender']) || $this->getUser()->hasPreference('gender'))
            && (isset($data['exercise_location']) || $this->getUser()->hasPreference('exercise_location_id'))
            && (isset($data['exercise_frequency']) || $this->getUser()->hasPreference('exercise_frequency_id'))
        ) {
            return true;
        }
        return false;
    }

    /**************************************************
    *
    * CALCULATORS
    *
    ***************************************************/

    /**
     * Update the calorie goal. (Calculated from steps and weight).
     *
     * @param integer weight
     * @param integer steps
     * @param object ExerciseLocation|null exerciseLocation
     * @param object ExerciseFrequency|null exerciseFrequency
     * @return self
     * @throws Exception
     */
    public function calculateCalorieGoal($weight, $steps, $exerciseLocation, $exerciseFrequency)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('Unable to calculate targets. No user set.');
        }

        // Floor the weight so we dont slip between brackets
        $weight = floor($weight);
        $calories = 0;

        // Retrieve the file
        if ($this->getUser()->getPreference('gender') == 'Male') {
            $targets = $this->getCsvData('male_calorie_goal.csv');
            $workoutIncreases = $this->getCsvData('male_calorie_workout_increase.csv');
        } elseif ($this->getUser()->getPreference('gender') == 'Female') {
            $targets = $this->getCsvData('female_calorie_goal.csv');
            $workoutIncreases = $this->getCsvData('female_calorie_workout_increase.csv');
        }

        // Work out which target
        foreach ($targets as $target) {
            if ($target['steps'] == $steps && $target['weight_low'] <= $weight && $target['weight_high'] >= $weight) {
                // Set the target
                $calories = $target['calories'];
            }
        }

        // Work out which additional calories from workout
        foreach ($workoutIncreases as $workoutIncrease) {
            $workoutOption = strtolower($exerciseLocation->title) . '_' . $exerciseFrequency->amount;
            if (
                $workoutIncrease['workout_option'] == $workoutOption
                && $workoutIncrease['weight_low'] <= $weight
                && $workoutIncrease['weight_high'] >= $weight
            ) {
                $calories += $workoutIncrease['calorie_increase'];
            }
        }

        // Check and throw exception if we have no calories
        if (!isset($calories)) {
            throw new FitnessBadRequestException(
                'Cannot calculate macros, no calorie information available. Please contract Team RH Support.'
            );
        }

        return $calories;
    }

    /**
     * Calculate the exercise level. (Calculated from steps, exercise location and gender).
     *
     * @param integer weight
     * @param object ExerciseLocation|null exerciseLocation
     * @param object ExerciseFrequency|null exerciseFrequency
     * @return self
     * @throws Exception
     */
    public function calculateExerciseLevel($weight, $exerciseLocation, $exerciseFrequency)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('Unable to calculate targets. No user set.');
        }

        // Retrieve the file
        if ($this->getUser()->getPreference('gender') == 'Male') {
            $workoutIncreases = $this->getCsvData('male_calorie_workout_increase.csv');
        } elseif ($this->getUser()->getPreference('gender') == 'Female') {
            $workoutIncreases = $this->getCsvData('female_calorie_workout_increase.csv');
        }

        // Work out which additional calories from workout
        $calories = 0;
        foreach ($workoutIncreases as $workoutIncrease) {
            $workoutOption = strtolower($exerciseLocation->title) . '_' . $exerciseFrequency->amount;
            if (
                $workoutIncrease['workout_option'] == $workoutOption
                && $workoutIncrease['weight_low'] <= $weight
                && $workoutIncrease['weight_high'] >= $weight
            ) {
                $calories = $workoutIncrease['calorie_increase'];
            }
        }

        // Retrieve the exercise level
        if ($calories > 0) {
            return ExerciseLevel::where('title', '=', 'Athletic')->firstOrFail();
        } else {
            return ExerciseLevel::where('title', '=', 'Standard')->firstOrFail();
        }
    }

    public function getCustomGoals()
    {
        if (!$this->getUser()) {
            throw new Exception('No user set');
        }
        if (!$this->canCalculateGoals([])) {
            return [];
        }
        $currentNutrition = $this->getNutritionGoals();
        $baselineNutrition = $this->getBaselineNutrition();
        foreach ($currentNutrition as $type => $value) {
            $currentNutrition[$type] = $baselineNutrition[$type] != $value;
        }
        return $currentNutrition;
    }

    public function getNutritionGoals()
    {
        foreach ($this->getGoalsArray() as $type) {
            $currentNutrition[$type] = $this->getUser()->getPreference('daily_' . $type . '_goal');
        };
        return $currentNutrition;
    }

    public function getGoalsArray()
    {
        return array('calorie', 'protein', 'carbohydrate', 'fat', 'fiber');
    }

    public function getBaselineNutrition()
    {
        $nutritionGoals = $this->getGoalsArray();
        $baselineNutrition['calorie'] = (int) $this->calculateCalorieGoal(
            $this->getUser()->getPreference('start_weight'),
            $this->getUser()->getPreference('daily_step_goal'),
            ExerciseLocation::find($this->getUser()->getPreference('exercise_location_id')),
            ExerciseFrequency::find($this->getUser()->getPreference('exercise_frequency_id'))
        );
        foreach ($nutritionGoals as $type) {
            if ($type != 'calorie') {
                $baselineNutrition[$type] = (int) $this->calculateNutritionGoal($type, $baselineNutrition['calorie']);
            }
        }
        return $baselineNutrition;
    }

    public function calculateNutritionGoal($type, $calories)
    {
        if (!$this->getUser()) {
            throw new Exception('Unable to calculate targets. No user set.');
        }

        // Retrieve the file
        if ($this->getUser()->getPreference('gender') == 'Male') {
            $targets = $this->getCsvData('male_nutrition_goal.csv');
        } elseif ($this->getUser()->getPreference('gender') == 'Female') {
            $targets = $this->getCsvData('female_nutrition_goal.csv');
        }
        // Work out which target
        $goal = [];
        foreach ($targets as $target) {
            if ($target['calories'] <= $calories) {
                // Set the targets

                if ($type == 'carbohydrate') {
                    $type = 'carbs';
                }
                $goal = $target[$type];
            }
        }

        if (!empty($goal)) {
            return $goal;
        }

        throw new FitnessBadRequestException(
            'Cannot calculate macros, no nutritional information available. Please contract Team RH Support.'
        );
    }


    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the user associated to the instance of the service.
     *
     */
    public function getUser()
    {
        return isset($this->user) ? $this->user : null;
    }

    /**
     * Retrieve a CSV targets file and orient into array.
     *
     * @param string
     * @return array
     */
    private function getCsvData($csv)
    {
        $csvService = new CsvService('app/csv/' . $csv);
        return $csvService->toArray();
    }


    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/

    /**
     * Set the user associated to the instance of the service.
     *
     * @param User $user
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
