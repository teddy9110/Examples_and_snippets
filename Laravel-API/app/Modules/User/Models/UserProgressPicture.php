<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgressPicture extends Model
{
    protected $table = 'user_progress_pictures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_progress_id',
        'uuid',
        'type',
        'original_name',
        'path',
        'public',
        'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation to progress.
     */
    public function progress()
    {
        return $this->belongsTo(UserProgress::class, 'user_progress_id', 'id');
    }
}
