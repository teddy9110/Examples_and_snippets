<?php

namespace Rhf\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductBundles extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'introduction_text',
        'closing_text',
        'bundle_slug',
        'bundle'
    ];

    protected $casts = [
        'bundle' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($bundle) {
                $bundle->bundle_slug = Str::slug(strtolower($bundle->title)) . '_bundle';
            }
        );
    }
}
