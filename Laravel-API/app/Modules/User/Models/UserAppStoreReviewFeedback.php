<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\AppReviewTopic;

class UserAppStoreReviewFeedback extends Model
{
    protected $table = 'user_app_store_review_feedbacks';

    protected $fillable = [
        'score',
        'review_id',
        'comments',
    ];

    public function review()
    {
        return $this->belongsTo(UserAppStoreReview::class, 'review_id');
    }

    public function topics()
    {
        return $this->belongsToMany(
            AppReviewTopic::class,
            'pivot_user_app_review_feedback_topics',
            'feedback_id',
            'topic_id'
        );
    }
}
