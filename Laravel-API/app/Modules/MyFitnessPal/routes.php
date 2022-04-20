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

// Authenticate routes
Route::group([
    'prefix' => 'api/1.0/my-fitness-pal',
    'module' => 'MyFitnessPal',
    'namespace' => 'Rhf\Modules\MyFitnessPal\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {

    // Auth and access endpoints
    Route::get('/auth', 'MyFitnessPalController@auth');
    Route::get('/status', 'MyFitnessPalController@status');

    // Sync data
    Route::get('/sync/{date}', 'MyFitnessPalController@sync');

    // Unlink
    Route::delete('', 'MyFitnessPalController@unlink');
});

// Web routes
Route::group([
    'module' => 'MyFitnessPal',
    'namespace' => 'Rhf\Modules\MyFitnessPal\Controllers',
    'middleware' => ['web']
], function () {

    // Response URL for auth
    Route::get('/api/1.0/auth/my-fitness-pal', 'MyFitnessPalController@authComplete');

    // Test endpoint
    Route::get('/mfp', 'MyFitnessPalController@test');

    // MFP down page
    Route::get('/status/myfitnesspal.com', 'MyFitnessPalController@mfpStatus');
});
