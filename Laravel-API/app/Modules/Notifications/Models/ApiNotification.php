<?php

namespace Rhf\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class ApiNotification extends Model
{
    protected $table = 'api_notifications';

    public function users()
    {
        return $this->hasManyThrough(
            User::class, // TABLE I WANT
            UserApiNotification::class, // THROUGH
            'notification_id', // what I'm looking for on THROUGH in RELATION TO THIS TABLE
            'id', // column on RELATED TABLE
            'id', // CURRENT TABLE column
            'user_id' // ON THROUGH TABLE
        );
    }
}
