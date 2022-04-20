<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rhf\Modules\Workout\Decorators\HasThumbnailAndVideo;

class Exercise extends Model
{
    use HasThumbnailAndVideo;
    use SoftDeletes;

    protected $table = 'exercise';

    protected $fillable = [
        'title',
        'descriptive_title',
        'content',
        'video',
        'content_video',
        'thumbnail',
        'content_thumbnail',
    ];

    protected $hidden = [];

    public function workoutRoundExercises()
    {
        return $this->hasMany(RoundExercise::class, 'exercise_id');
    }
}
