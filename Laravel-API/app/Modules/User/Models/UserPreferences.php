<?php

namespace Rhf\Modules\User\Models;

use Database\Factories\UserPreferencesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'weight_unit', 'gender', 'dob', 'daily_step_goal', 'start_height', 'start_weight',
        'exercise_location_id', 'exercise_frequency_id', 'daily_water_goal', 'daily_calorie_goal',
        'exercise_level_id', 'daily_protein_goal', 'daily_carbohydrate_goal', 'daily_fat_goal',
        'daily_fiber_goal', 'personal_goals', 'medical_conditions', 'user_role', 'marketing_email_consent',
        'medical_conditions_consent', 'tutorial_complete', 'progress_picture_consent', 'period_tracker'
    ];

    protected $casts = [
        'period_tracker' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserPreferencesFactory::new();
    }

    /**
     * Relation to user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function getDailyWaterGoalAttribute()
    {
        if (is_null($this->attributes['daily_water_goal'])) {
            return $this->attributes['daily_water_goal'];
        }

        // As the new type is `double`, it needs to be cast to `int` to be backwards compatible.
        // Using intval() on a `double` can cause issues, e.g. intval on `double` 201.0 will return 200.
        // See https://stackoverflow.com/a/58712745 for an example.
        // We use round on the double value to work around this. For older API versions it will not be anything
        // less than a full integer value, hence it's safe to round it.
        if (api_version() >= 20210914) {
            return intval(round($this->attributes['daily_water_goal'] * 200));
        } else {
            return intval(round($this->attributes['daily_water_goal']));
        }
    }

    public function setDailyWaterGoalAttribute($value)
    {
        if (api_version() >= 20210914 && !is_null($value)) {
            $this->attributes['daily_water_goal'] = $value / 200;
        } else {
            $this->attributes['daily_water_goal'] = $value;
        }
    }
}
