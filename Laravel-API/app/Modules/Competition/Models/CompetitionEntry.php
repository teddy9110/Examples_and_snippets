<?php

namespace Rhf\Modules\Competition\Models;

use Database\Factories\CompetitionEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Rhf\Modules\Competition\Services\CompetitionImageService;
use Rhf\Modules\System\Traits\Filterable;
use Rhf\Modules\User\Models\User;

class CompetitionEntry extends Model
{
    use SoftDeletes;
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'competition_id',
        'user_id',
        'url',
        'votes',
        'reports',
        'suspended'
    ];

    protected $casts = [
        'suspended' => 'boolean'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return CompetitionEntryFactory::new();
    }

    public function getImage($image)
    {
        $competitionImageService = new CompetitionImageService($this);
        return $competitionImageService->getImage($image);
    }

    /**
     * User Relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Competition relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_id', 'id');
    }

    public function reportDetails()
    {
        return $this->hasMany(CompetitionReports::class, 'entry_id', 'id');
    }

    public function voted($id)
    {
        return CompetitionVotes::where('user_id', Auth::id())->where('entry_id', $id)->exists();
    }

    public function reported($id)
    {
        return CompetitionReports::where('user_id', Auth::id())->where('entry_id', $id)->exists();
    }
}
