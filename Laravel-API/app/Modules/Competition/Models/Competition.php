<?php

namespace Rhf\Modules\Competition\Models;

use Carbon\Carbon;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Rhf\Modules\Competition\Services\CompetitionImageService;
use Rhf\Modules\System\Traits\Filterable;

class Competition extends Model
{
    use SoftDeletes;
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'description',
        'desktop_image',
        'mobile_image',
        'app_image',
        'rules',
        'prize',
        'start_date',
        'end_date',
        'active',
        'closed'
    ];

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
        'closed' => 'boolean',
    ];

    protected $plainKeys = [
        'title',
        'subtitle',
        'description',
        'rules',
        'prize',
        'start_date',
        'end_date',
        'active',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return CompetitionFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($competition) {
                $competition->slug = Str::slug($competition->title, '-');
            }
        );

        static::updating(
            function ($competition) {
                $competition->slug = Str::slug($competition->title, '-');
            }
        );
    }

    public function getImage($image)
    {
        $competitionImageService = new CompetitionImageService($this);
        return $competitionImageService->getImage($image);
    }

    public function convertDate($date)
    {
        $converted = new Carbon($date);
        return $converted->format('Y-m-d\TH:i:sO\Z');
    }

    /**
     * Entries relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(CompetitionEntry::class)->orderBy('created_at', 'desc');
    }

    /**
     * winners relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function winner()
    {
        return $this->hasOne(CompetitionWinners::class, 'competition_id', 'id')
            ->with(['user', 'entry']);
    }

    public function leaderboard()
    {
        return $this->hasMany(CompetitionEntry::class)->with('user')->limit(10)->orderBy('votes', 'desc');
    }

    public function getPlainKeys()
    {
        return $this->plainKeys;
    }
}
