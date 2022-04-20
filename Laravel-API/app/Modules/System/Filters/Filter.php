<?php

namespace Rhf\Modules\System\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    protected $filters;
    /**
     * Builder Instance
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Filter constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;
        foreach ($this->filters as $column => $value) {
            if (method_exists($this, $column)) {
                call_user_func_array([$this, $column], array_filter([$value]));
            }
        }
        return $this->builder;
    }
}
