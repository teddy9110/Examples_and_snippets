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

// API endpoints WITHOUT Auth
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'User',
    'namespace' => 'Rhf\Modules\User\Controllers',
    'middleware' => ['api']
], function () {
    // Endpoints used in TEST
    Route::post('/user/new-pending', 'UserController@createPendingUser');
    Route::post('/account/status', 'UserController@userAccountStatus');
});

// API endpoints with Auth
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'User',
    'namespace' => 'Rhf\Modules\User\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    Route::post('/app-review-feedback', 'UserAppStoreReviewController@userFeedbackSubmitted');
    Route::post('/app-review', 'UserAppStoreReviewController@userLeftReview');
    Route::get('/app-review', 'UserAppStoreReviewController@checkUserEligibility');
    Route::get('/app-feedback-topics', 'UserAppStoreReviewController@getFeedbackTopics');

    // User and account details
    Route::get('/account/details', 'UserController@getDetails');
    Route::patch('/account/details', 'UserController@updateDetails');

    Route::get('/account/period-tracker', 'UserController@getPeriodTrackingStatus');
    Route::put('/account/period-tracker', 'UserController@setPeriodTrackingStatus');
    //User Workouts
    Route::put('/account/workout-preferences', 'WorkoutPreferenceController@setWorkoutPreferences');
    Route::get('/account/workout-preferences', 'WorkoutPreferenceController@getWorkoutPreferences');


    // User targets
    Route::get('/account/targets', 'UserController@getTargets');

    // User Progress Pictures
    Route::post('/account/progress', 'UserController@createProgressPictures');
    Route::get('/account/progress', 'UserController@getProgressPictures');
    Route::get('/account/progress/{id}', 'UserController@getProgressPicture');
    Route::patch('/account/progress/{id}', 'UserController@editProgressPicture');
    Route::post('/account/progress/consent/{type}', 'UserController@consentProgress');
    Route::delete('/account/progress/{id}', 'UserController@deleteProgressPicture');

    // Return user upsell products
    Route::get('/account/bundles', 'UserController@onboardProductBundle');

    // Mark tutorial complete
    Route::post('/account/tutorial', 'UserController@markTutorialComplete');

    // Post questionnaire results
    Route::post('/account/questionnaire', 'UserController@userQuestionnaire');
});

// API endpoint used by external Team RH Fitness store page
Route::group([
    'prefix' => 'api/1.0',
    'module' => 'User',
    'namespace' => 'Rhf\Modules\User\Controllers',
    'middleware' => ['api', 'api_key']
], function () {
    // NEVER DELETE THIS. Used to create user accounts when they purchase life plan.
    Route::post('/user/create', 'UserController@managePendingUser');
});

Route::group([
    'prefix' => 'api/1.0/direct-debit',
    'module' => 'User',
    'namespace' => 'Rhf\Modules\User\Controllers',
    'middleware' => ['api', 'api_key']
], function () {
    Route::post('/users', 'DirectDebitController@upsertUser');
    Route::get('/users/by-email/{email}', 'DirectDebitController@getUserByEmail');
    Route::post('/users/by-email', 'DirectDebitController@getUsersByEmail');
    Route::get('/users/by-id/{id}', 'DirectDebitController@getUserById');
    Route::post('/users/{id}/paid', 'DirectDebitController@markUserPaid');
    Route::post('/users/{id}/not-paid', 'DirectDebitController@markUserNotPaid');

    Route::post('/users/update-expiries', 'DirectDebitController@bulkUpdateExpiries');
});

Route::group([
    'prefix' => 'api/1.0/carb-questionnaire',
    'module' => 'User',
    'namespace' => 'Rhf\Modules\User\Controllers',
    'middleware' => ['api', 'auth:api']
], function () {
    Route::get('/gender', 'CarbQuestionnaireController@gender');
    Route::get('/carb-goal', 'CarbQuestionnaireController@carbGoal');
    Route::get('/weekly-average/{type}', 'CarbQuestionnaireController@weeklyAverage');
});
