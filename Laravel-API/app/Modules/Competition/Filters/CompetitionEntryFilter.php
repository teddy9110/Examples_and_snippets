<?php

namespace Rhf\Modules\Competition\Filters;

use Illuminate\Database\Eloquent\Builder;
use Rhf\Modules\System\Filters\Filter;

class CompetitionEntryFilter extends Filter
{
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

    public function include(string $value = null): Builder
    {
        return $this->builder->where('suspended', 0)
            ->orWhere('suspended', 1);
    }
}
