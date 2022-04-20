<?php

namespace Rhf\Modules\Activity\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Rhf\Modules\System\Services\CacheService;
use Rhf\Modules\User\Models\User;

class ActivityCache extends CacheService
{
    /** @var User $user */
    protected User $user;

    /** @var array $cacheableActivities */
    protected static array $cacheableActivities = ['steps', 'water', 'weight'];

    public static $expiryTtl = 604800;

    public function __construct(User $user)
    {
        parent::__construct('activity:' . $user->id);
        $this->user = $user;
    }

    public function cacheProgress($date)
    {
        $progressService = new ProgressService();
        $progressService->setUser($this->user)->from($date)->to((clone $date));
        $progress = $progressService->dailyProgress();
        $this->setCache($this->createCacheKey('daily_progress', $date), $progress, static::$expiryTtl);
        return $progress;
    }

    /**
     * Create a key to store values in cache against.
     * Returns singular or weekly key
     *
     * @param string $type
     * @param boolean $weekly
     * @return string
     */
    public function createCacheKey(string $type, string $date, bool $weekly = false): string
    {
        $key = $type . ':' . $this->parseDate($date)->format('Y-m-d');
        if ($weekly) {
            $weekDate = Carbon::parse($date);
            $key .= ':' . $weekDate->copy()->startOfWeek() . ':' . $weekDate->copy()->endOfWeek();
        }
        return $key;
    }

    /**
     * Return a cache key for a period of time
     *
     * @param string $type
     * @param string $period
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return string
     */
    public function createPeriodCacheKey(string $period, string $type, Carbon $startDate, Carbon $endDate)
    {
        return $type . ':' . $period . ':' .
            $startDate->format('Y-m-d') . ':' . $endDate->format('Y-m-d');
    }

    /**
     * Wildcard search for keys with a matching type
     * if date intersects a date of a cached range
     * delete the cached key
     *
     * @param string $type
     * @param string $date
     * @return
     */
    public function findActivityKeys(string $type, string $date): array
    {
        $matchingKeys = $this->findKeys();

        $matchingActivityKeys = [];
        foreach ($matchingKeys as $value) {
            $explode = explode(':', $value);
            // Need to check if the key has a start/end date
            if ($explode[0] == $type && Str::contains($explode[1], ['period', 'long', 'week', 'month', 'all'])) {
                $slice = array_slice($explode, -2, 2);
                $check = $this->parseDate($date)->between(
                    $this->parseDate($slice[0]),
                    $this->parseDate($slice[1])
                );

                if ($check) {
                    $matchingActivityKeys[] = $value;
                }
            }
        }
        return $matchingActivityKeys;
    }

    /**
     * Check if the activity type is cacheable.
     *
     * @param mixed $type
     * @return bool
     */
    public static function isCacheableActivity($type)
    {
        return in_array($type, static::$cacheableActivities);
    }

    /**
     * Create a carbon instance of a date using parse
     *
     * @param string $date
     * @return Carbon
     */
    private function parseDate(string $date): Carbon
    {
        return Carbon::parse($date);
    }
}
