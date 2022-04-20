<?php

namespace Rhf\Modules\Activity\Models;

use Carbon\Carbon;
use Database\Factories\Activity\ActivityFactory;
use Rhf\Modules\Activity\Services\ActivityCache;
use Rhf\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\System\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use Filterable;
    use HasFactory;

    protected $table = 'activity';

    public const CALCULATION_TYPE_SUM = 'sum';
    public const CALCULATION_TYPE_APPEND = 'append';
    public const CALCULATION_TYPE_REPLACE = 'replace';

    // Types of activity that should add to the existing day's value
    public static $sum = ['water'];
    // Types of activity that get appended daily
    public static $append = ['exercise'];
    // Types of activity that get replaced daily
    public static $replace = ['steps','calories','fiber','fat','protein','weight','carbohydrates'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'value',
        'date',
    ];

    protected $hidden = [];

    protected $dates = ['date', 'created_at', 'updated_at'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ActivityFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Activity $activity) {
            self::activityCaching($activity);
        });

        static::saved(function (Activity $activity) {
            self::activityCaching($activity);
        });
    }

    /**
     * @param Activity $activity
     * @return void
     */
    private static function activityCaching(Activity $activity): void
    {
        $activityCache = new ActivityCache(auth('api')->user());

        $originalDate = $activity->getOriginal('date');
        $differentDates = !is_null($originalDate) && !$originalDate->isSameDay($activity->date);

        // Re-cache daily progress
        $activityCache->deleteCache($activityCache->createCacheKey('daily_progress', $activity->date));
        if ($differentDates) {
            $activityCache->deleteCache($activityCache->createCacheKey('daily_progress', $originalDate));
        }

        // Delete stored cache with overlapping dates
        $keysFromWildCardSearch = $activityCache->findActivityKeys($activity->type, $activity->date);
        foreach ($keysFromWildCardSearch as $value) {
            if (is_string($value)) {
                $activityCache->deleteCache($value);
            }
        }
    }

    public function scopeLatestWeightActivity($query)
    {
        return $query->where('type', '=', 'weight')->latest('created_at');
    }

    public function getValueAttribute()
    {
        if ($this->attributes['type'] == 'water' && api_version() >= 20210914) {
            return is_null($this->attributes['value']) ? null : strval($this->attributes['value'] * 200);
        }
        return $this->attributes['value'];
    }

    public function setValueAttribute($value)
    {
        if ($this->attributes['type'] == 'water' && api_version() >= 20210914) {
            // 3 decimals, because of mls to cup conversion.
            // 1 cup = 200 ml, 1 ml = 0.005 cups, 2ml = 0.010, etc. It should never go over 3 decimal points.
            $this->attributes['value'] = number_format($value / 200, 3, '.', '');
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /*
    *
    * RELATIONSHIPS
    *
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }


    public function notes()
    {
        return $this->hasOne(ActivityNotes::class, 'activity_id', 'id');
    }

    /*
    *
    * STATIC
    *
    */

    /**
     * Retrieve which type of create record mechanism is used when adding a new log
     *
     * @param string type
     *
     * @return string
     */
    public static function getCalculationType($type)
    {
        if (in_array($type, self::$sum)) {
            return self::CALCULATION_TYPE_SUM;
        }
        if (in_array($type, self::$append)) {
            return self::CALCULATION_TYPE_APPEND;
        }
        if (in_array($type, self::$replace)) {
            return self::CALCULATION_TYPE_REPLACE;
        }
    }
}
