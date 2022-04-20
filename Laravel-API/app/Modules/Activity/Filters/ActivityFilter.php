<?php

namespace Rhf\Modules\Activity\Filters;

use Illuminate\Database\Eloquent\Builder;
use Rhf\Modules\System\Filters\Filter;

class ActivityFilter extends Filter
{
    protected $type = '';

    /**
     * Filter by type
     *
     * @param string|null $value
     * @return Builder
     */
    public function type(string $value = null): Builder
    {
        $this->type = $value;
        return $this->builder->where('type', $value);
    }

    /**
     * Filter by Date Range
     *
     * @param array $value
     * @return Builder
     */
    public function range(array $value = []): Builder
    {
        if (isset($value['start_date'])) {
            $this->builder = $this->builder->where('date', '>=', $value['start_date']);
        }

        if (isset($value['end_date'])) {
            $this->builder = $this->builder->where('date', '<=', $value['end_date']);
        }

        return $this->builder;
    }

    public function order(array $value = []): Builder
    {
        if (!isset($value['sort_by'])) {
            return $this->builder;
        }

        return $this->builder->orderBy(
            $value['sort_by'],
            $value['sort_direction'] ?? 'asc'
        );
    }
}
