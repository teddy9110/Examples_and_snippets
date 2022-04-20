<?php

namespace Rhf\Modules\Video\Filters;

use Illuminate\Database\Eloquent\Builder;
use Rhf\Modules\System\Filters\Filter;

class VideoFilter extends Filter
{
    public function order(array $value = []): Builder
    {
        if (!isset($value['sort_by'])) {
            return $this->builder;
        }
        $sort = $this->sortBy($value['sort_by']);

        return $this->builder->orderBy(
            $sort,
            $value['sort_direction'] ?? 'asc'
        );
    }

    public function include(string $value = null): Builder
    {
        return $this->builder->orderBy($value, 'desc')
            ->orderBy('created_at', 'desc');
    }


    public function tags(array $value = []): Builder
    {
        if (!isset($value)) {
            return $this->builder;
        }
        return $this->builder->whereHas('tags', function ($q) use ($value) {
            $q->whereIn('tags.slug', $value);
        });
    }

    /**
     * @param $sortBy
     * @return string
     */
    private function sortBy($sortBy): string
    {
        switch ($sortBy) {
            case 'recent':
                $sort = 'created_at';
                break;
            case 'watched':
                $sort = 'open_count';
                break;
        }
        return $sort;
    }
}
