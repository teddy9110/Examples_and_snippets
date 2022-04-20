<?php

namespace Rhf\Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserSubscriptions;

class AppleSubscriptions extends Model
{
    protected $table = 'apple_subscriptions_data';

    protected $fillable = [
        'purchase_date',
        'original_transaction_id',
        'current_transaction_id',
        'product_id',
        'bundle_id',
        'receipt_data',
        'auto_renew',
        'is_trial',
        'intro_offer'
    ];

    public function subscription()
    {
        return $this->hasOne(UserSubscriptions::class, 'subscription_reference', 'current_transaction_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscriptions::class, 'apple_original_transaction_id', 'original_transaction_id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserSubscriptions::class,
            'subscription_reference',
            'id',
            'current_transaction_id',
            'user_id'
        );
    }
}
