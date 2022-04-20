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

// DEPRECATED:
// Replace with WorkoutsController.
// exercise-categories & exercise-levels are not used.
// iOS app has a reference to exercise-levels, but is never called.
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Exercise',
    'namespace' => 'Rhf\Modules\Exercise\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    // Retrieve workout
    // Route::get('/workouts', 'ExerciseController@userCategories');  // Moved to workouts module
    Route::get('/workout/{category_id}', 'ExerciseController@workout');

    // Retrieve meta data around exercises
    Route::get('/exercise-categories', 'ExerciseController@categories')->middleware('facebook');
    Route::get('/exercise-levels', 'ExerciseController@levels')->middleware('facebook');
});
