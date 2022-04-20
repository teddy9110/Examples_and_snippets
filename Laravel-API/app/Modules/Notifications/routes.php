<?php

Route::group(
    [
        'prefix' => 'api/1.0',
        'module' => 'Notifications',
        'namespace' => 'Rhf\Modules\Notifications\Controllers',
        'middleware' => ['api', 'auth:api']
    ],
    function () {
        Route::group(['prefix' => 'notifications'], function () {
            Route::put('/token', 'NotificationController@userTokenInitialization');
            Route::get('/user-preferences', 'NotificationController@userNotificationPreferences');
            Route::patch('/user-preferences', 'NotificationController@userNotificationPreferences');
            Route::delete('/logout', 'NotificationController@userTokenLogout');
        });

        Route::group(['prefix' => 'api-notifications'], function () {
            Route::get('/', 'ApiNotificationController@getNotifications');
            Route::post('/{id}', 'ApiNotificationController@acknowledgeNotification');
        });
    }
);
