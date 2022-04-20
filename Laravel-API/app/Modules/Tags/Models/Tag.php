<?php

namespace Rhf\Modules\Tags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Video\Models\Video;

class Tag extends Model
{
    protected $fillable = [
        'name', 'slug', 'type'
    ];

    //automatic slug when creating a tag
    public static function boot()
    {
        parent::boot();
        static::creating(
            function ($tag) {
                $tag->slug = Str::slug($tag->name, '_');
            }
        );
    }

    /**
     * Many to Many on user/tags
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_tags');
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'video_tags');
    }
}
