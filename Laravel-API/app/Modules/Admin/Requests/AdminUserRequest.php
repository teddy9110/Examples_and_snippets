<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use Rhf\Modules\User\Enums\Gender;
use Rhf\Modules\User\Enums\WeightUnit;

class AdminUserRequest extends Request
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
        return [
            'email' => [
                $this->method() === 'POST' ? 'required' : 'sometimes',
                'email',
                $this->method() === 'POST' ? 'unique:users,email' : ''
            ],
            'password' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'paid' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
            'expiry_date' => [$this->method() === 'POST' ? 'required' : 'sometimes', 'date'],
            'role_id' => [$this->method() === 'POST' ? 'required' : 'sometimes', 'exists:user_roles,id'],

            'profile.first_name' => 'sometimes|string|max:255',
            'profile.last_name' => 'sometimes|string|max:255',
            'profile.dob' => 'sometimes|date',
            'profile.gender' => [
                'sometimes',
                Rule::in(Gender::getValues())
            ],
            'profile.medical_conditions' => 'sometimes|string',
            'profile.personal_goals' => 'sometimes|string',
            'profile.start_weight' => 'sometimes|numeric',
            'profile.start_height' => 'sometimes|numeric',

            'preferences.weight_unit' => [
                'sometimes',
                Rule::in(WeightUnit::getValues())
            ],
            'preferences.exercise_location_id' => 'sometimes|exists:exercise_location,id',
            'preferences.exercise_level_id' => 'sometimes|exists:exercise_level,id',
            'preferences.exercise_frequency_id' => 'sometimes|exists:exercise_frequency,id',

            'staff_user' => 'sometimes|bool',

            'goals.daily_step_goal' => 'sometimes|integer',
            'goals.daily_water_goal' => 'sometimes|numeric',
            'goals.daily_calorie_goal' => 'sometimes|numeric',
            'goals.daily_protein_goal' => 'sometimes|numeric',
            'goals.daily_carbohydrate_goal' => 'sometimes|numeric',
            'goals.daily_fat_goal' => 'sometimes|numeric',
            'goals.daily_fiber_goal' => 'sometimes|numeric',
        ];
    }
}
