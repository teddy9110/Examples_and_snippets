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

// Unauthenticated routes
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Content',
    'namespace' => 'Rhf\Modules\Content\Controllers',
    'middleware' => ['api', 'facebook']
], function () {
    Route::get('/content/video/{id}', 'ContentController@getVideoUrl');
});

// Content slug videos such as life-plan outside of Facebook hosted on AWS S3
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Content',
    'namespace' => 'Rhf\Modules\Content\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    Route::get('/content/{slug}', 'ContentController@retrieveContentVideos')->where(['slug' => '[A-Za-z\-]+']);
});

// Authenticate routes
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Content',
    'namespace' => 'Rhf\Modules\Content\Controllers',
    'middleware' => ['api', 'auth:api', 'facebook']
], function () {
    // Retrieve the content
    Route::get('/content/search/{term}', 'ContentController@search');
    Route::get('/content/{category_id?}/{content_id?}', 'ContentController@get');
});
