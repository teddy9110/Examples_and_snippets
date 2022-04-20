<?php

if (config('app.env') != 'production') {
    Route::group([
        'prefix' => 'api/1.0/competitions',
        'module' => 'Competition',
        'namespace' => 'Rhf\Modules\Competition\Controllers',
        'middleware' => ['api', 'auth:api']
    ], function () {
        Route::get('/', 'CompetitionController@index');
        Route::get('/user-entries', 'EntryController@userEntries');
        Route::get('/{id}', 'CompetitionController@show');
        Route::get('/{competition_id}/entries', 'EntryController@getEntries');
        Route::post('/{competition_id}/entries', 'EntryController@submitEntry');
    });

    Route::group([
        'prefix' => 'api/1.0',
        'module' => 'Competition',
        'namespace' => 'Rhf\Modules\Competition\Controllers',
        'middleware' => ['api', 'auth:api']
    ], function () {
        Route::get('/competition-entries/{entry_id}', 'EntryController@getEntry');
        Route::post('/competition-entries/{entry_id}/edit', 'EntryController@editEntry');
        Route::get('/competition-entries', 'EntryController@userCompetitionEntry');
        Route::delete('/competition-entries/{entry_id}', 'EntryController@deleteEntry');
        Route::post('/competition-entries/{entry_id}/report', 'EntryController@report');
        Route::post('/competition-entries/{entry_id}/vote', 'EntryController@entryVote');
    });
}
