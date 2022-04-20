<?php

namespace Rhf\Modules\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class ActivityDeletion extends Model
{
    protected $table = 'activity_deletions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'value',
        'date',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'date',
        'deleted_at'
    ];
}
