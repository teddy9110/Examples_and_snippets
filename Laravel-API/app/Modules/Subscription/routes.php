<?php

// API endpoints with Auth
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Subscription', 'namespace' => 'Rhf\Modules\Subscription\Controllers',
    'middleware' => ['api']
], function () {
    Route::get('/subscriptions', 'SubscriptionController@availableSubscriptions');
    Route::get('/subscription/{product_id}', 'SubscriptionController@retrieveSubscription');
    Route::post('/subscription/apply', 'SubscriptionController@applySubscription');
});

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Subscription',
    'namespace' => 'Rhf\Modules\Subscription\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    Route::post('/apple/verify', 'ReceiptVerificationController@appleValidation');
});
