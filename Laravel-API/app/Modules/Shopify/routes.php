<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

// API endpoints WITHOUT Auth
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Shopify',
    'namespace' => 'Rhf\Modules\Shopify\Controllers',
    'middleware' => ['api']
], function () {
    Route::post('/shopify/webhook/order-paid', 'ShopifyController@orderPaid');

    Route::view('/shopify/email/welcome-instructions', 'orders.welcome-email', [
        'name' => 'Test User',
        'date' => now()->format('d/m/Y'),
    ]);

    Route::view('/shopify/email/welcome-annual', 'orders.welcome-annual-email', [
        'name' => 'Test User',
        'date' => now()->format('d/m/Y'),
    ]);

    Route::get('/shopify/suggested', 'PrismicController@suggested');
    Route::get('/shopify/promoted', 'PrismicController@promoted');
});
