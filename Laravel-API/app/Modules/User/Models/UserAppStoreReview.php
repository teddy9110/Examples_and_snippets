<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserAppStoreReview extends Model
{
    protected $table = 'user_app_store_reviews';

    protected $fillable = [
        'user_id',
        'present_review_dialog',
        'next_review_request',
        'last_review_submitted',
        'user_response',
    ];

    protected $dates = [
        'next_review_request',
        'last_review_submitted',
    ];

    protected $casts = [
        'present_review_dialog' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(UserAppStoreReviewFeedback::class, 'review_id');
    }
}
