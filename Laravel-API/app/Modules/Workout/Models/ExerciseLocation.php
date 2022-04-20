<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseLocation extends Model
{
    public const SLUG_GYM = 'gym';
    public const SLUG_HOME = 'home';

    protected $table = 'exercise_location';

    protected $fillable = [
        'title',
        'slug',
    ];

    protected $hidden = [];

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'exercise_location_id');
    }
}
