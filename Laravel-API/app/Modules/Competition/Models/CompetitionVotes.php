<?php

namespace Rhf\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Competition\Models\CompetitionEntry;
use Rhf\Modules\User\Models\User;

class CompetitionVotes extends Model
{
    protected $fillable = [
        'entry_id',
        'user_id',
    ];

    public function entry()
    {
        return $this->hasOne(CompetitionEntry::class, 'entry_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
