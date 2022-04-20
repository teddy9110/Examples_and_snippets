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

// API endpoints with Auth
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Product',
    'namespace' => 'Rhf\Modules\Product\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/products/promoted/placements', 'ProductController@placements');
    Route::get('/products/promoted', 'ProductController@promoted');
});
