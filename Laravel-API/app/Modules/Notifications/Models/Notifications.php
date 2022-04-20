<?php

namespace Rhf\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifications extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'link',
        'data',
        'send_at',
        'topic_id',
        'subtopic_id'
    ];

    protected $plainKeys = [
        'title',
        'content',
        'image',
        'link',
        'data',
        'send_at',
        'topic_id',
        'subtopic_id'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function topic()
    {
        return $this->hasOne(Topics::class, 'id', 'topic_id');
    }

    public function subtopic()
    {
        return $this->hasOne(SubTopics::class, 'id', 'subtopic_id');
    }

    /**
     * Get the plain keys.
     */
    public function getPlainKeys()
    {
        return $this->plainKeys;
    }
}
