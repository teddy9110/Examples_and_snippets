<?php

namespace Rhf\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class UserApiNotification extends Model
{
    protected $fillable = [
        'user_id',
        'notification_id'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function notifications()
    {
        return $this->belongsTo(ApiNotification::class, 'id', 'notification_id');
    }
}
