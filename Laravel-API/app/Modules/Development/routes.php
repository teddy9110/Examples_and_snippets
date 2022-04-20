<?php

// API endpoints with Auth
Route::group([
    'prefix' => 'api/1.0/development',
    'module' => 'Development',
    'namespace' => 'Rhf\Modules\Development\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {
    if (config('app.env') != 'production') {
        Route::post('/create-seeded-user', 'UserController@createUser');
        Route::post('/delete-seeded-users', 'UserController@removeUsers');
        Route::get('/create-recipe', 'RecipeController@createRecipe');
        Route::get('/create-bad-recipe', 'RecipeController@createBadRecipe');
        Route::post('/create-video', 'VideoController@createVideo');
        Route::post('/seed-user-activities', 'ActivitiesController@createActivitesForUserBetweenDates');
        Route::post('/set-daily-medal', 'ActivitiesController@setDailyMedal');
        Route::get('send-in-blue-emails/{user}', 'ActivitiesController@setDailyMedal');
    }
});
