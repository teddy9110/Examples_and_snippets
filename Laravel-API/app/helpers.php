<?php

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Rhf\Modules\System\Models\Feature;
use Rhf\Modules\System\Models\Setting;

if (!function_exists('api_version')) {
    /**
     * Utility method to retrieve API version from request header
     *
     * @return int
     */
    function api_version()
    {
        return (int)request()->header('api-version');
    }
}

if (!function_exists('grhaft_enabled')) {
    /**
     * Utility function to check if GRHAFT feature is enabled.
     *
     * TODO: After GRHAFT is enabled, deprecate the use of it and eventually remove.
     */
    function grhaft_enabled()
    {
        $setting = Setting::where('meta', 'features')->first();
        if (is_null($setting) || !isset($setting->value)) {
            return false;
        }
        $features = json_decode($setting->value, true);
        return isset($features['grhaft']) ? (bool) $features['grhaft'] : false;
    }
}

if (!function_exists(('workouts_v3_available'))) {
    /**
     * Helper method to determine whether Workouts V3 is available for the incoming request.
     * 1) Api-Version header value is 20211217 or above;
     * 2) workouts_v3 feature is enabled.
     *
     * @return bool
     * @throws BindingResolutionException
     */
    function workouts_v3_available()
    {
        return api_version() >= 20211217 && feature_enabled('workouts_v3');
    }
}

if (!function_exists('get_date_interval')) {
    /**
     * Utility Method to get date intervals
     *
     * @param string $period
     * @return string|void
     */
    function get_date_interval($period = 'week')
    {
        switch ($period) {
            case 'day':
                return 'P1D';
            case 'week':
                return 'P1W';
            case 'month':
                return 'P1M';
            default:
                return 'P1W';
        }
    }
}

if (!function_exists('get_period_range')) {
    /** Utility method to return a range of date periods.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $period
     * @return DatePeriod
     */
    function get_period_range(Carbon $startDate, Carbon $endDate, $period = 'week')
    {
        $startOfPeriod = $startDate->copy()->startOf($period);
        $endOfPeriod = $endDate->copy()->endOf($period);
        return new \DatePeriod(
            new \DateTime($startOfPeriod),
            new \DateInterval(get_date_interval($period)),
            new \DateTime($endOfPeriod)
        );
    }
}

if (!function_exists('get_pagination_period_range')) {
    /**
     * Utility method to generate a date range for the provided period, based on pagination properties.
     *
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param Carbon $startDate Start date
     * @param Carbon $endDate End date
     * @param string $period Date period - 'week' or 'month'
     * @param string $sortDirection Sort direction - 'asc' (default) or 'desc'
     * @return DatePeriod
     */
    function get_pagination_period_range(
        int $page,
        int $perPage,
        Carbon $startDate,
        Carbon $endDate,
        string $period = 'week',
        string $sortDirection = 'asc'
    ) {
        $startOfPeriod = $startDate->copy()->startOf($period);
        $endOfPeriod = $endDate->copy()->endOf($period);
        $overflow = $period == 'month' ?: false;

        if ($sortDirection == 'asc') {
            $start = $startOfPeriod->copy()->add($period, ($page - 1) * $perPage, $overflow);
            $end = $endOfPeriod->copy()->add($period, $page * $perPage, $overflow);
            if ($end->gt($endOfPeriod)) {
                $end = $endOfPeriod->copy();
            }
        } else {
            $end = $endOfPeriod->copy()->sub($period, ($page - 1) * $perPage, $overflow);
            $start = $endDate->copy()->startOf($period)->sub($period, $page * $perPage, $overflow)->add($period, 1);
            if ($start->lt($startDate)) {
                $start = $startOfPeriod->copy();
            }
        }

        return get_period_range($start, $end, $period);
    }
}

if (!function_exists('iso_date')) {
    function iso_date($date)
    {
        $converted = new Carbon($date);
        return $converted->toISOString();
    }
}

if (!function_exists('add_position')) {
    function add_position($competition)
    {
        $voteCount = $competition->mapWithKeys(
            function ($value, $key) use ($competition) {
                $toAdd = 1;
                if ($competition instanceof LengthAwarePaginator && $competition->currentPage() != 1) {
                    $toAdd = ($competition->currentPage() * $competition->perPage()) - $competition->perPage() + 1;
                }
                return [$key + $toAdd => $value['votes']];
            }
        )->toArray();


        $competition->each(
            function ($value) use ($voteCount) {
                $value['position'] = array_search($value['votes'], $voteCount);
                $value['tied'] = array_count_values($voteCount)[$value['votes']] >= 2 ?? false;
            }
        );
        return $competition;
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $total, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            $options
        );
    }
}


if (!function_exists('feature_enabled')) {
    function feature_enabled($slug)
    {
        $feature = Feature::where('slug', $slug)->first();
        if (!$feature) {
            return false;
        }
        return $feature->active;
    }
}
