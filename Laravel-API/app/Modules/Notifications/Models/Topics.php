<?php

namespace Rhf\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topics extends Model
{
    protected $fillable = [
        'category',
        'slug',
        'description'
    ];

    protected $plainKeys = [
        'category',
        'description'
    ];

    //automatic slug when creating a topic
    public static function boot()
    {
        parent::boot();
        static::creating(
            function ($topic) {
                $topic->slug = Str::slug(strtolower($topic->category), '_');
            }
        );
    }

    public function notifications()
    {
        return $this->hasMany(Notifications::class, 'topic_ids', 'id');
    }

    public static function getIdBySlug($slug)
    {
        return self::where('slug', $slug)->firstOrFail()->id;
    }

    public function subtopics()
    {
        return $this->hasMany(SubTopics::class, 'topic_id', 'id');
    }
}
