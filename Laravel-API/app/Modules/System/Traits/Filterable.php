<?php

namespace Rhf\Modules\System\Traits;

use Illuminate\Database\Eloquent\Builder;
use Rhf\Modules\System\Filters\Filter;

trait Filterable
{
    /**
     * Apply relevant filters
     *
     * @param Builder $query
     * @param Filter $filter
     * @return Builder
     */
    public function scopeFilter(Builder $query, Filter $filter): Builder
    {
        return $filter->apply($query);
    }
}
