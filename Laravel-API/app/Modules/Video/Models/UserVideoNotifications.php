<?php

namespace Rhf\Modules\Video\Models;

use Illuminate\Database\Eloquent\Model;

class UserVideoNotifications extends Model
{
    public $fillable = [
        'user_id',
        'notifications_read'
    ];
}
