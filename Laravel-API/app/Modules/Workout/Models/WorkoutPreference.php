<?php

namespace Rhf\Modules\Workout\Models;

use Database\Factories\UserWorkoutPreferencesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class WorkoutPreference extends Model
{
    use HasFactory;

    protected $table = 'user_workout_preferences';

    protected $fillable = [
        'user_id',
        'schedule',
        'exercise_level_id',
        'exercise_location_id',
        'exercise_frequency_id',
        'data',
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserWorkoutPreferencesFactory::new();
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function exerciseFrequency()
    {
        return $this->belongsTo(ExerciseFrequency::class);
    }

    public function exerciseLevel()
    {
        return $this->belongsTo(ExerciseLevel::class);
    }

    public function exerciseLocation()
    {
        return $this->belongsTo(ExerciseLocation::class);
    }
}
