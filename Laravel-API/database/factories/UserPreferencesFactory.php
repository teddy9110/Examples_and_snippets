<?php

namespace Database\Factories;

use Rhf\Modules\User\Models\UserPreferences;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferencesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPreferences::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $gender = array_rand(array_flip(['Male', 'Female']));
        $weight = rand(150, 230);

        $steps = array_rand(array_flip(['5000', '10000', '15000', '20000', '25000']));

        return [
            'user_id' => '',
            'weight_unit' => 'lb',
            'gender' => $gender,
            'dob' => date('Y-m-d', (mt_rand(1, 1104497999))),
            'daily_step_goal' => $steps,
            'start_height' => rand('140', '210'),
            'start_weight' => $weight,
            'daily_water_goal' => 8,
            'daily_calorie_goal' => 1,
            'daily_protein_goal' => 1,
            'daily_carbohydrate_goal' => 1,
            'daily_fat_goal' => 1,
            'daily_fiber_goal' => 1,
            'personal_goals' => '',
            'medical_conditions' => '',
            'user_role' => '',
            'token' => '',
            'mfp_access_token' => '',
            'mfp_refresh_token' => '',
            'mfp_token_expires_at' => '',
            'mfp_user_id' => '',
            'mfp_authentication_code' => '',
            'marketing_email_consent' => array_rand([0, 1]),
            'medical_conditions_consent' => array_rand([0, 1]),
            'tutorial_complete' => array_rand([0, 1]),
//            'progress_picture_consent' => array_rand(array_flip(['unknown', 'accepted', 'rejected'])),
            'period_tracker' => array_rand([0, 1]),
        ];
    }
}
