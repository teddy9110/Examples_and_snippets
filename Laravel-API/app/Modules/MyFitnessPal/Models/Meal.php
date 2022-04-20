<?php

namespace Rhf\Modules\MyFitnessPal\Models;

use Carbon\Carbon;

class Meal
{
    public $diary_meal; // Which meal is this (Breakfast, Lunch, Dinner, Snacks)
    public $date; // Date of diary entry (Carbon object)
    public $nutritional_contents;
                    /* Sample
                    [protein] => 37
                    [fat] => 15.4
                    [saturated_fat] => 6.2
                    [polyunsaturated_fat] => 0
                    [monounsaturated_fat] => 0
                    [trans_fat] => 0
                    [cholesterol] => 0
                    [sodium] => 1.89
                    [potassium] => 0
                    [fiber] => 1.7
                    [sugar] => 12.2
                    [vitamin_a] => 0
                    [vitamin_c] => 0
                    [calcium] => 0
                    [iron] => 0
                    [carbohydrates] => 39.6
                    [energy] => stdClass Object
                        (
                            [unit] => calories
                            [value] => 524
                        )*/

    public function __construct($meal)
    {
        // Build the object
        $this->diary_meal = $meal->diary_meal;
        $this->date = Carbon::parse($meal->date);
        $this->nutritional_contents = $meal->nutritional_contents;
    }
}
