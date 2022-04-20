<?php

namespace Rhf\Modules\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class ActivityNotes extends Model
{
    protected $table = 'activity_details';

    protected $fillable = [
        'user_id',
        'note',
        'date',
        'activity_id',
        'period',
        'body_fat_percentage'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id', 'activity_id');
    }
}
