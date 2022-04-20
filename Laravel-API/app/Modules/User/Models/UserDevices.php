<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'firebase_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
