<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Models\ExerciseLocation;

class UserDetailsRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.

     * @return array
     */
    public function rules()
    {
        if (!api_version()) {
            $locationRules = 'in:Gym,Home';
        } else {
            $locationRules = Rule::in([
                ExerciseLocation::SLUG_GYM,
                ExerciseLocation::SLUG_HOME,
            ]);
        }

        if (!workouts_v3_available()) {
            $frequencyRules = Rule::in([
                ExerciseFrequency::SLUG_0,
                ExerciseFrequency::SLUG_3,
                ExerciseFrequency::SLUG_6,
            ]);
        } else {
            $gymFrequencies = [
                ExerciseFrequency::SLUG_0,
                ExerciseFrequency::SLUG_3,
                ExerciseFrequency::SLUG_6,
            ];
            $homeFrequencies = [
                ExerciseFrequency::SLUG_0,
                ExerciseFrequency::SLUG_3,
                ExerciseFrequency::SLUG_5,
            ];
            $frequencyRules = Rule::in(
                $this->json('exercise_location') == ExerciseLocation::SLUG_GYM
                    ? $gymFrequencies
                    : $homeFrequencies
            );
        }


        if (!workouts_v3_available()) {
            $levelRules = 'in:standard,athletic';
        } else {
            $levelRules = 'sometimes|nullable|in:standard,athletic';
        }

        switch ($this->method()) {
            case 'PATCH':
                return [
                    'first_name' => 'string',
                    'surname' => 'string',
                    'dob' => 'date_format:Y-m-d',
                    'gender' => 'in:Male,Female',
                    'start_weight' => 'numeric',
                    'start_height' => 'numeric',
                    'daily_step_goal' => 'integer',
                    'daily_calorie_goal' => 'integer',
                    'daily_water_goal' => 'integer|max:1999999|min:0',
                    'exercise_level_id' => 'integer|exists:exercise_level,id',
                    'exercise_location' => $locationRules,
                    'exercise_frequency' => $frequencyRules,
                    'exercise_level' => $levelRules,
                    'password' => 'string|min:8',
                    'weight_unit' => 'in:kg,st,lb',
                    'marketing_email_consent' => 'boolean',
                    'medical_conditions' => 'string|nullable',
                    'medical_conditions_consent' => 'boolean',
                    'personal_goals' => 'string|nullable',
                    'progress_picture_consent' => 'in:unknown,accepted,rejected',
                    'period_tracker' => 'boolean',
                ];
            case 'GET':
            case 'DELETE':
            case 'PUT':
            case 'POST':
                return [];
            default:
                break;
        }
    }
}
