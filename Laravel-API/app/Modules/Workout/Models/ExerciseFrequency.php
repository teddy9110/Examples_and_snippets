<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Workout\Models\Workout;

class ExerciseFrequency extends Model
{
    public const SLUG_0 = 0;
    public const SLUG_3 = 3;
    public const SLUG_5 = 5;
    public const SLUG_6 = 6;

    protected $table = 'exercise_frequency';

    protected $fillable = [
        'amount',
    ];

    protected $hidden = [];

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'exercise_frequency_id');
    }
}
