<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Subscription\Models\AppleSubscriptions;

class UserSubscriptions extends Model
{
    public const SUBSCRIPTION_TYPE_APPLE = 'apple';
    public const SUBSCRIPTION_TYPE_GOCARDLESS = 'gocardless';
    public const SUBSCRIPTION_TYPE_ASHBOURNE = 'directdebit';
    public const SUBSCRIPTION_TYPE_SMARTDEBIT = 'smartdebit';
    public const SUBSCRIPTION_TYPE_SHOPIFY = 'shopify';
    public const SUBSCRIPTION_TYPE_FREE = 'free';

    protected $fillable = [
        'user_id',
        'email',
        'subscription_provider',
        'subscription_plan',
        'subscription_frequency',
        'purchase_date',
        'expiry_date',
        'shopify_customer_id',
        'subscription_reference',
        'apple_original_transaction_id'
    ];

    protected $dates = [
        'purchase_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function apple()
    {
        return $this->hasOne(AppleSubscriptions::class, 'current_transaction_id', 'subscription_reference');
    }

    public function apples()
    {
        return $this->hasMany(AppleSubscriptions::class, 'original_transaction_id', 'apple_original_transaction_id');
    }
}
