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

// Unauthenticated Routes
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'System',
    'namespace' => 'Rhf\Modules\System\Controllers',
    'middleware' => ['api']
], function () {

    // API health check
    Route::get('/', 'SystemController@healthCheck');

    // Reset the facebook error blocker
    Route::get('/system/unblock-facebook', 'SettingController@unblockFacebook');

    // Get app versions for given platform
    Route::get('/app/{platform}/versions', 'AppController@showVersion');
});

// Authenticate routes
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'System',
    'namespace' => 'Rhf\Modules\System\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {

    // Retrieve the content
    Route::get('/setting/{slug}', 'SettingController@get');

    // Get latest info
    Route::get('/app/{platform}/status/{date?}', 'AppController@getStatus');

    Route::get('/config', 'ConfigController@config');
});

// Web routes
Route::group([
    'module' => 'System',
    'namespace' => 'Rhf\Modules\System\Controllers',
    'middleware' => ['web']
], function () {

    // Terms and conditions view
    Route::get('/tcs', 'SystemController@tcs');

    // privacy policy view
    Route::get('/privacy-policy', 'SystemController@privacyPolicy');
});

Route::group([
    'prefix' => 'api/1.0/features',
    'module' => 'System',
    'namespace' => 'Rhf\Modules\System\Controllers',
    'middleware' => ['api']
], function () {
    Route::get('/', 'FeatureController@index');
    Route::get('/{slug}', 'FeatureController@featureBySlug');
});
