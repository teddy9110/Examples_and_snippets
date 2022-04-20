<?php

namespace Rhf\Modules\MyFitnessPal\Models;

use Carbon\Carbon;

class Day
{
    public $meals; // Collection of Meal objects
    public $date; // Date of diary entry (Carbon object)

    public function __construct($meals)
    {
        // Build the object
        $this->meals = $meals;
        if ($meals->count()) {
            $this->date = Carbon::parse($meals->first()->date);
        }
    }

    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /*
     * Function getCalories
     *
     * get the total calories for the day
     *
     * @return (int)
     */
    public function getCalories()
    {
        if (isset($this->calories)) {
            return $this->calories;
        }

        // Loop meals and create total calories
        $this->calories = 0;
        foreach ($this->meals as $meal) {
            $this->calories += $meal->nutritional_contents->energy->value;
        }
        return $this->calories;
    }

    /*
     * Function getProtein
     *
     * get the total protein for the day
     *
     * @return (float)
     */
    public function getProtein()
    {
        if (isset($this->protein)) {
            return $this->protein;
        }

        // Loop meals and create total calories
        $this->protein = 0;
        foreach ($this->meals as $meal) {
            $this->protein += $meal->nutritional_contents->protein;
        }
        return $this->protein;
    }

    /*
     * Function getFat
     *
     * get the total fat for the day
     *
     * @return (float)
     */
    public function getFat()
    {
        if (isset($this->fat)) {
            return $this->fat;
        }

        // Loop meals and create total calories
        $this->fat = 0;
        foreach ($this->meals as $meal) {
            $this->fat += $meal->nutritional_contents->fat;
        }
        return $this->fat;
    }

    /*
     * Function getFiber
     *
     * get the total fiber for the day
     *
     * @return (float)
     */
    public function getFiber()
    {
        if (isset($this->fiber)) {
            return $this->fiber;
        }

        // Loop meals and create total calories
        $this->fiber = 0;
        foreach ($this->meals as $meal) {
            $this->fiber += $meal->nutritional_contents->fiber;
        }
        return $this->fiber;
    }

    /*
     * Function getCarbohydrates
     *
     * get the total carbohydrates for the day
     *
     * @return (float)
     */
    public function getCarbohydrates()
    {
        if (isset($this->carbohydrates)) {
            return $this->carbohydrates;
        }

        // Loop meals and create total calories
        $this->carbohydrates = 0;
        foreach ($this->meals as $meal) {
            $this->carbohydrates += $meal->nutritional_contents->carbohydrates;
        }
        return $this->carbohydrates;
    }
}
