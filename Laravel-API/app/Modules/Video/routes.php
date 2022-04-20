<?php

Route::group([
   'prefix' => 'api/1.0/video',
   'module' => 'Video',
   'namespace' => 'Rhf\Modules\Video\Controllers',
   'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/', 'VideoController@index');
    Route::get('/new', 'VideoController@new');
    Route::get('/daily', 'VideoController@getByDate');
    Route::get('/tags', 'VideoController@getTags');
    Route::get('/{id}', 'VideoController@show');
    Route::post('/view/{id}', 'VideoController@watch');
    Route::post('/new', 'VideoController@acknowledge');
});
