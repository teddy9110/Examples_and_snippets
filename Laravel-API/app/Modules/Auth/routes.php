<?php

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Auth',
    'namespace' => 'Rhf\Modules\Auth\Controllers',
    'middleware' => ['api']
], function () {
    Route::post('/auth/login', 'AuthController@login');
    Route::post('/account/signup', 'AuthController@signup');
    Route::get('/auth/token-refresh', 'AuthController@refresh');
});

/*
 * Password Reset routes
 */
Route::group([
    'module' => 'Auth',
    'namespace' => 'Rhf\Modules\Auth\Controllers',
    'middleware' => ['web']
], function () {
    // Admin password reset route for web
    Route::post('/password/reset', 'AuthController@reset')->name('password.reset.admin');

    // Token route for setting new password
    Route::get('/password/reset/{token}', 'AuthController@showResetForm')->name('password.send-reset-email');

    // Success route for successful change of password
    Route::get('password/success', 'AuthController@success')->name('password.reset.success');
});

Route::group([
    'prefix' => 'api/1.0',
    'middleware' => ['api']
], function () {
    // User password reset route for API
    Route::post('password/reset', [
        'as' => 'password.reset',
        'uses' => 'Rhf\Modules\User\Controllers\PasswordController@sendReset'
    ]);
});

Route::group([
    'prefix' => 'admin',
    'module' => 'Admin',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['web']
], function () {
    // Admin password reset route for web
    Route::get('/password/reset', '\Rhf\Modules\Auth\Controllers\AuthController@showResetForm');
});
