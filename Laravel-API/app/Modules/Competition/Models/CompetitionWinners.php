<?php

namespace Rhf\Modules\Competition\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;

class CompetitionWinners extends Model
{
    protected $fillable = [
        'competition_id',
        'entry_id'
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_id', 'id');
    }

    public function entry()
    {
        return $this->belongsTo(CompetitionEntry::class, 'entry_id', 'id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            CompetitionEntry::class,
            'id',
            'id',
            'entry_id',
            'user_id'
        );
    }
}
