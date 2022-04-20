<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoundExercise extends Model
{
    use SoftDeletes;

    protected $table = 'workout_round_exercises';

    protected $fillable = [
        'order',
        'repeat',
        'quantity',
    ];

    protected $hidden = [];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }
}
