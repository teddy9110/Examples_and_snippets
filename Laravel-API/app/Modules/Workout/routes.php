<?php

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Workout',
    'namespace' => 'Rhf\Modules\Workout\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/workouts/by-date/{date}', 'WorkoutsController@workoutByDate');
    Route::get('/workouts', 'WorkoutsController@userWorkouts');
    Route::get('/workouts/{id}', 'WorkoutsController@workout');
});
