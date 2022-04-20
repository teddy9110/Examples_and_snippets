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
    'module' => 'Recipe',
    'namespace' => 'Rhf\Modules\Recipe\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    // Recipe resource
    Route::resource('recipes', 'RecipeController')->only([
        'index', 'show'
    ]);

    Route::get('/recipe/favourites', 'RecipeController@getUserFavourites');
    Route::post('/recipe/favourite', 'RecipeController@toggleFavouriteRecipe');
});
