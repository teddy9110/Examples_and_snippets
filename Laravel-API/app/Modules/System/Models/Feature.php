<?php

namespace Rhf\Modules\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feature extends Model
{
    protected $fillable = [
        'name', 'slug', 'active', 'active_from',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(
            function ($feature) {
                $feature->slug = Str::slug(strtolower($feature->name), '_');
            }
        );
    }
}
