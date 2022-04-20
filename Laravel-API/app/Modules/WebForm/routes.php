<?php

Route::group([
   'prefix' => 'api/1.0/web',
   'module' => 'WebForm',
   'namespace' => 'Rhf\Modules\WebForm\Controllers',
   'middleware' => ['api']
], function () {
    // Success Stories
    Route::post('/user-transformations-stories', 'WebFormController@stories');
    Route::post('/zendesk', 'WebZendeskController@createSupportTicket');
    Route::get('/shopify', 'WebShopifyCarousel@index');
});


if (config('app.env') != 'production') {
    Route::group([
        'prefix' => 'api/1.0/competition',
        'module' => 'WebForm',
        'namespace' => 'Rhf\Modules\WebForm\Controllers',
        'middleware' => ['api']
    ], function () {
        Route::get('/', 'WebCompetitionController@getAllCompetitions');
        Route::get('/{slug}', 'WebCompetitionController@getCompetition');
        Route::get('{slug}/entries', 'WebCompetitionController@getCompetitionEntries');
        Route::get('entries/{id}', 'WebCompetitionController@getEntry');
        Route::get('{slug}/leaderboard', 'WebCompetitionController@getCompetitionLeaderboard');
        Route::post('entry/vote/{id}', 'WebCompetitionController@entryVote')->middleware('throttle:20,1');
        Route::post('entry/downvote/{id}', 'WebCompetitionController@removeEntryVote')->middleware('throttle:20,1');
    });
}
