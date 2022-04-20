<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class StaffNote extends Model
{
    protected $table = 'staff_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'note','logged_by', 'last_updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation to user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
