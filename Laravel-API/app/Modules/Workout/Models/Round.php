<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Workout\Decorators\HasThumbnailAndVideo;

class Round extends Model
{
    use HasThumbnailAndVideo;

    protected $table = 'workout_rounds';

    protected $fillable = [
        'title',
        'content',
        'order',
        'repeat',
        'thumbnail',
        'content_thumbnail',
        'video',
        'content_video',
    ];

    protected $hidden = [];

    public function workout()
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }

    public function roundExercises()
    {
        return $this->hasMany(RoundExercise::class, 'round_id')->orderBy('order');
    }
}
