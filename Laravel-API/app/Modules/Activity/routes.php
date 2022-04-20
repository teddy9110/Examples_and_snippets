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

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Activity',
    'namespace' => 'Rhf\Modules\Activity\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    // Add new activity data
    Route::post('/log/exercise/{date?}', 'ActivityController@postExercise');
    Route::post('/log/workout/{date?}', 'ActivityController@postWorkout');
    Route::post('/log/steps/{date?}', 'ActivityController@postSteps');
    Route::post('/log/water/{date?}', 'ActivityController@postWater');
    Route::post('/log/weight/{date?}', 'ActivityController@postWeight');

    // Delete activity data
    Route::delete('/log/weight/{date}', 'ActivityController@deleteWeight');

    // Retrieve activity data
    Route::get('/log/steps/{start_date}/{end_date}', 'ActivityController@stepsLog');
    Route::get('/log/water/{start_date}/{end_date}', 'ActivityController@waterLog');
    Route::get('/log/weight/{start_date}/{end_date}', 'ActivityController@weightLog');
    Route::get('/log/calories/{start_date}/{end_date}', 'ActivityController@caloriesLog');
    Route::get('/log/protein/{start_date}/{end_date}', 'ActivityController@proteinLog');
    Route::get('/log/fat/{start_date}/{end_date}', 'ActivityController@fatLog');
    Route::get('/log/fiber/{start_date}/{end_date}', 'ActivityController@fiberLog');
    Route::get('/log/carbohydrates/{start_date}/{end_date}', 'ActivityController@carbohydratesLog');

    // Retrieve Achievements
    Route::get('/achievement/medal/{date}/day', 'AchievementController@medalByDay');
    Route::get('/achievement/medal/{date}/week', 'AchievementController@medalByWeek');
    Route::get('/achievement/medal/overview', 'AchievementController@overview');
    Route::get('/achievement/medal/historical', 'AchievementController@historicalMedals');

    // Progress
    Route::get('/progress/daily/{date}', 'ActivityController@dailyProgress');
    Route::get('/progress/fiber/{date}', 'ActivityController@dailyFiberProgress');
    Route::get('/progress/protein/{date}', 'ActivityController@dailyProteinProgress');
    Route::get('/progress/calories/{date}', 'ActivityController@dailyCaloriesProgress');
    Route::get('/progress/steps/{date}', 'ActivityController@dailyStepsProgress');
    Route::get('/progress/water/{date}', 'ActivityController@dailyWaterProgress');

    // Average
    Route::get('/average/{category}', 'ActivityController@averageLog');

    //total weight loss endpoint
    Route::get('/progress/weight-loss', 'ActivityController@weightLossLog');

    //Notes
    Route::delete('/activity/note/{id}', 'ActivityController@deleteNote');

    //period tracker - accepts user_id
    Route::get('/activity/period/{id}', 'ActivityController@getUserPeriods');

    // New Activity Logging
    Route::post('/activities', 'ActivitiesController@createActivity');
    Route::patch('/activities/{id}', 'ActivitiesController@updateActivity');
    Route::get('/activities', 'ActivitiesController@getActivities');
    Route::get('/activities/{id}', 'ActivitiesController@getActivity');
    Route::delete('/activities/{id}', 'ActivitiesController@deleteActivity');
    Route::get('activities/average/{category}', 'ActivityController@averageLog');
});
