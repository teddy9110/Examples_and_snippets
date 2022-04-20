<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppReviewTopic extends Model
{
    use HasFactory;

    protected $table = 'app_review_topics';

    protected $fillable = [
        'review_id',
        'title',
        'topic_id',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return AppReviewTopic::new();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($topic) {
                $topic->slug = Str::slug($topic->title, '-');
            }
        );
    }
}
