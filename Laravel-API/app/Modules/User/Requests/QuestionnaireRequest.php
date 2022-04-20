<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'weight_resistance_workouts_per_week' => 'sometimes|integer',
            'workout_with_rh_app' => 'sometimes|boolean',
            'own_workouts' => 'sometimes|nullable|string|max:150',
            'issues_preventing_workouts' => 'sometimes|nullable|string|max:150',
            'lifting_as_heavy_as_possible' => 'sometimes|boolean',
            'taking_progress_photos' => 'sometimes|boolean',
            'achieve_steps' => 'sometimes|string|max:150',
            'step_goal_increased_days' => 'sometimes|nullable|integer',
            'hunger_level' => 'sometimes|integer|min:1|max:10',
            'period_due_in_days' => 'sometimes|nullable|integer',
            'started_medication' => 'sometimes|boolean',
            'processed_food' => 'sometimes|boolean',
            'workouts_in_weeks' => 'sometimes|integer',
            'changed_workouts' => 'sometimes|boolean',
            'platform' => ['sometimes', 'regex:/^(ios|android)$/i'],
            'app_version' => 'sometimes',
        ];
    }
}
