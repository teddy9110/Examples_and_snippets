<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'weight_value',
        'date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation to progress picture.
     */
    public function progressPicture()
    {
        return $this->hasMany(UserProgressPicture::class, 'user_progress_id', 'id');
    }

    /**
     * Relation to user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
