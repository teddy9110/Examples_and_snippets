<?php

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Zendesk',
    'namespace' => 'Rhf\Modules\Zendesk\Controllers',
    'middleware' => ['api']
], function () {
    Route::post('/zendesk/jwt', 'ZendeskController@generate');
});

Route::group([
    'prefix' => 'api/1.0',
    'module' => 'Zendesk',
    'namespace' => 'Rhf\Modules\Zendesk\Controllers',
     'middleware' => ['api', 'auth:api']
], function () {
    Route::post('/zendesk/article', 'ZendeskController@article');
    Route::get('/zendesk/article/{slug}', 'ZendeskController@article');

    Route::get('/zendesk/unread', 'ZendeskController@unread');
    Route::post('/zendesk/has-open-ticket', 'ZendeskController@hasOpenTicket');
    Route::get('/zendesk/user-tickets', 'ZendeskController@usersTickets');
    Route::get('/zendesk/ticket/{id}', 'ZendeskController@getTicket');
    Route::post('/zendesk/ticket', 'ZendeskController@createTicket')->name('create');
    Route::post('/zendesk/ticket/{id}', 'ZendeskController@updateTicket')->name('update');
});
