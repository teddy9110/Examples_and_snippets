<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseLevel extends Model
{
    public const SLUG_STANDARD = 'standard';
    public const SLUG_ATHLETIC = 'athletic';

    protected $table = 'exercise_level';

    protected $fillable = [
        'title',
        'slug',
    ];

    protected $hidden = [
        'pivot'
    ];

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'exercise_frequency_id');
    }
}
