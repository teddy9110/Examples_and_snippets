<?php

// API endpoints with Auth
Route::group([
    'prefix' => 'api/1.0/admin',
    'module' => 'Admin',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {

    Route::group(['prefix' => 'users'], function () {
        Route::get('/permissions', 'AdminUserController@permissions')->middleware('can:list:users');
        Route::get('/roles', 'AdminUserController@roles')->middleware('can:list:users');
        Route::get('/me', 'AdminUserController@whoAmI')->middleware('can:view:user');
        Route::get('/', 'AdminUserController@index')->middleware('can:list:users');
        Route::post('/', 'AdminUserController@store')->middleware('can:add:user');
        //TODO: Delete once deprecated on FE
        Route::delete('/notes/{noteId}', 'AdminUserController@deleteStaffNote')->middleware('can:update:user');
        // TODO: Delete once deprecated on FE
        Route::put('/notes/{noteId}', 'AdminUserController@updateStaffNote')->middleware('can:update:user');
        Route::get('/{id}', 'AdminUserController@show')->middleware('can:view:user');
        Route::put('/{id}', 'AdminUserController@update')->middleware('can:update:user');
        Route::patch('/{id}', 'AdminUserController@update')->middleware('can:update:user');
        Route::delete('/{id}', 'AdminUserController@delete')->middleware('can:delete:user');
        Route::delete('/{id}/purge', 'AdminUserController@purge')->middleware('can:delete:user');
        Route::patch('/{id}/restore', 'AdminUserController@restore')->middleware('can:restore:user');
        Route::get('/{id}/graphs/{type}', 'AdminUserController@showGraph')->middleware('can:view:user');
        Route::get('/{id}/achievements', 'AdminUserController@showAchievements')->middleware('can:view:user');

        // TODO: Delete once deprecated on FE
        Route::get('/{id}/notes', 'AdminUserController@showStaffNotes')->middleware('can:view:user');
        // TODO: Delete once deprecated on FE
        Route::post('/{id}/notes', 'AdminUserController@createStaffNote')->middleware('can:update:user');
        Route::get('/{id}/progress', 'AdminUserController@showProgress')->middleware('can:view:user');
        Route::delete('/{id}/unlink-mfp', 'AdminUserController@unlinkMfp')->middleware('can:unlink-mfp:user');
        Route::get('/{id}/tags', 'AdminTagController@userTags'); //get Users tags
        Route::post('/{id}/tags', 'AdminTagController@toggleTagsOnUser'); // post users tags

        Route::get('/{id}/average', 'AdminUserController@userAverages');
        // activity downloads
        Route::post('/export-activities', 'AdminUserController@userDataDownload');
    });

    Route::group(['prefix' => 'exercises'], function () {
        Route::get('/', 'AdminExerciseController@index')->middleware('can:admin:exercises');
        Route::post('/', 'AdminExerciseController@store')->middleware('can:admin:exercises');
        Route::get('/frequencies', 'AdminExerciseController@frequencies')->middleware('can:view:exercise-preferences');
        Route::get('/levels', 'AdminExerciseController@levels')->middleware('can:view:exercise-preferences');
        Route::get('/locations', 'AdminExerciseController@locations')->middleware('can:view:exercise-preferences');
        Route::get('/{id}', 'AdminExerciseController@show')->middleware('can:admin:exercises');
        Route::put('/{id}', 'AdminExerciseController@update')->middleware('can:admin:exercises');
        Route::delete('/{id}', 'AdminExerciseController@destroy')->middleware('can:admin:exercises');
        Route::post('/{id}/thumbnail', 'AdminExerciseController@updateThumbnail')->middleware('can:admin:exercises');
        Route::post('/{id}/video', 'AdminExerciseController@updateVideo')->middleware('can:admin:exercises');
    });

    Route::group(['prefix' => 'workouts'], function () {
        Route::get('/', 'AdminWorkoutController@index')->middleware('can:admin:workouts');
        Route::post('/', 'AdminWorkoutController@store')->middleware('can:admin:workouts');
        Route::get('/{id}', 'AdminWorkoutController@show')->middleware('can:admin:workouts');
        Route::put('/{id}', 'AdminWorkoutController@update')->middleware('can:admin:workouts');
        Route::delete('/{id}', 'AdminWorkoutController@destroy')->middleware('can:admin:workouts');
        Route::post('/{id}/thumbnail', 'AdminWorkoutController@updateThumbnail')->middleware('can:admin:workouts');
        Route::post('/{id}/video', 'AdminWorkoutController@updateVideo')->middleware('can:admin:workouts');
        Route::post('/{id}/related-videos', 'AdminWorkoutController@syncRelatedVideos')
            ->middleware('can:admin:workouts');
    });

    Route::group(['prefix' => 'recipes'], function () {
        Route::get('/', 'AdminRecipeController@index')->middleware('can:list:recipes');
        Route::post('/', 'AdminRecipeController@store')->middleware('can:add:recipe');
        Route::get('/{id}', 'AdminRecipeController@show')->middleware('can:view:recipe');
        Route::put('/{id}', 'AdminRecipeController@update')->middleware('can:update:recipe');
        Route::delete('/{id}', 'AdminRecipeController@delete')->middleware('can:delete:recipe');
        Route::post('/{id}/image', 'AdminRecipeController@updateImage')->middleware('can:update:recipe');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'AdminCategoryController@index')->middleware('can:list:categories');
        Route::post('/', 'AdminCategoryController@store')->middleware('can:add:category');
        Route::get('/{id}', 'AdminCategoryController@show')->middleware('can:view:category');
        Route::put('/{id}', 'AdminCategoryController@update')->middleware('can:update:category');
        Route::delete('/{id}', 'AdminCategoryController@delete')->middleware('can:delete:category');
    });

    Route::group(['prefix' => 'content'], function () {
        Route::get('/', 'AdminContentController@index')->middleware('can:list:content');
        Route::post('/', 'AdminContentController@store')->middleware('can:add:content');
        Route::get('/{id}', 'AdminContentController@show')->middleware('can:view:content');
        Route::put('/{id}', 'AdminContentController@update')->middleware('can:update:content');
        Route::delete('/{id}', 'AdminContentController@delete')->middleware('can:delete:content');
    });

    Route::group(['prefix' => 'content/facebook'], function () {
        Route::get('/', 'AdminFacebookController@index')->middleware('can:view:facebook-content');
        Route::get('/video/{id}', 'AdminFacebookController@getVideoUrl')->middleware('can:view:facebook-content');
        Route::post('/{id}/add', 'AdminFacebookController@add')->middleware('can:add:facebook-content');
        Route::delete('/{id}/remove', 'AdminFacebookController@remove')->middleware('can:delete:facebook-content');

        Route::get('/login', 'AdminFacebookAuthController@authRedirectToFacebookProvider')
            ->middleware('can:view:facebook-content');
    });

    Route::group(['prefix' => 'facebook/videos'], function () {
        Route::get('/', 'AdminFacebookVideoController@index')->middleware('can:view:facebook-content');
        Route::get('/{id}', 'AdminFacebookVideoController@show')->middleware('can:view:facebook-content');
        Route::post('/', 'AdminFacebookVideoController@store')->middleware('can:add:facebook-content');
        Route::put('/{id}', 'AdminFacebookVideoController@update')->middleware('can:add:facebook-content');
        Route::delete('/{id}', 'AdminFacebookVideoController@remove')->middleware('can:delete:facebook-content');
        Route::post('/{id}/thumbnail', 'AdminFacebookVideoController@updateThumbnail')
            ->middleware('can:add:facebook-content');
    });

    Route::group(['prefix' => 'products/promoted'], function () {
        Route::get('/', 'AdminProductController@promotedProducts')->middleware('can:view:promoted-products');
        Route::post('/', 'AdminProductController@createPromotedProduct')->middleware('can:add:promoted-product');
        Route::get('/placements', 'AdminProductController@placements')->middleware('can:view:promoted-products');
        Route::get('/{id}', 'AdminProductController@showPromotedProduct')->middleware('can:view:promoted-products');
        Route::put('/{id}', 'AdminProductController@updatePromotedProduct')->middleware('can:update:promoted-product');
        Route::delete('/{id}', 'AdminProductController@deletePromotedProduct')
            ->middleware('can:delete:promoted-product');
        Route::post('/{id}/image', 'AdminProductController@updateImage')->middleware('can:update:promoted-product');
    });

    Route::group(
        ['prefix' => 'services'],
        function () {
            Route::get('/', 'AdminServiceController@index')->middleware('can:view:services');
            Route::get('/{slug}', 'AdminServiceController@show')->middleware('can:view:service');
            Route::post('/{slug}', 'AdminServiceController@updateService')->middleware('can:update:service');
        }
    );
    // User Subscriptions
    Route::group(['prefix' => 'subscriptions'], function () {
        Route::post('/', 'AdminSubscriptionController@createSubscription')
            ->middleware('can:manage:subscriptions');
    });

    Route::group(
        ['prefix' => 'management'],
        function () {
            Route::get('/', 'AdminManagementController@index')->middleware('can:view:dashboard');
            Route::get('/fixed-data', 'AdminManagementController@fixedPeriod')->middleware('can:view:dashboard');
            Route::get('/export', 'AdminManagementController@getUsersForCSVExport')->middleware('can:view:dashboard');
        }
    );

    // TODO: Add proper permissions for these
    if (config('app.env') != 'production') {
        Route::get('/reset-review-time', 'AdminUserController@resetReviewTime');
        Route::patch('/feature/{slug}', 'AdminFeatureController@toggleFeature')->middleware('can:update:service');
        Route::post('/users/{id}/reset-updates', 'AdminUserController@resetUpdates')->middleware('can:update:user');
        Route::post('/users/{id}/reset-consent', 'AdminUserController@resetConsent')->middleware('can:update:user');
        Route::post('/users/{id}/reset-goals', 'AdminUserController@resetUserGoals')->middleware('can:update:user');
        Route::post('/users/migrate-workouts', 'AdminUserController@migrateWorkouts')
            ->middleware('can:update:user');
        Route::post('/users/revert-migrate-workouts', 'AdminUserController@revertmigratedWorkouts')
            ->middleware('can:update:user');
        Route::post(
            '/clear-api-notifications',
            '\Rhf\Modules\Notifications\Controllers\ApiNotificationController@clear'
        )->middleware('can:update:user');

        Route::post('/users/{id}/factory', 'AdminMockController@createUserData');
        Route::post('/users/{id}/medals', 'AdminMockController@medalsFactory');
    }
    Route::group(['prefix' => 'subscriptions'], function () {
        Route::get('/', 'AdminSubscriptionController@index');
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'AdminNotificationController@index')->middleware('can:view:notification');
        Route::post('/', 'AdminNotificationController@createNotification')->middleware('can:update:notification');
        Route::get('/{id}', 'AdminNotificationController@showNotification')->middleware('can:view:notification');
        Route::put('/{id}', 'AdminNotificationController@updateNotification')->middleware('can:update:notification');
        Route::post('/{id}/send', 'AdminNotificationController@sendNow')->middleware('can:send:notification');
        Route::delete('{id}', 'AdminNotificationController@destroy')->middleware('can:delete:notification');
    });

    Route::group(['prefix' => 'topics'], function () {
        Route::get('/', 'AdminTopicController@index')->middleware('can:view:topic');
        Route::post('/', 'AdminTopicController@store')->middleware('can:update:topic');
        Route::get('{id}', 'AdminTopicController@show')->middleware('can:view:topic');
    });

    Route::group(['prefix' => 'subtopics'], function () {
        Route::get('/', 'AdminTopicController@subtopics')->middleware('can:view:topic');
        Route::post('/', 'AdminTopicController@createSubTopic')->middleware('can:update:topic');
        Route::get('/{id}', 'AdminTopicController@showSubtopic')->middleware('can:view:topic');
        Route::delete('{id}', 'AdminTopicController@destroy')->middleware('can:delete:topic');
    });

    Route::group(['prefix' => 'tags', 'middleware' => 'can:update:tags'], function () {
        Route::get('/', 'AdminTagController@index');
        Route::post('/', 'AdminTagController@createTag');
        Route::delete('{id}', 'AdminTagController@deleteTag');
    });

    Route::group(['prefix' => 'notes'], function () {
        Route::get('{userId}', 'AdminUserController@getUserNotes')->middleware('can:view:user');
        Route::post('/{userId}', 'AdminUserController@createStaffNote')->middleware('can:update:user');
        Route::put('{noteId}', 'AdminUserController@updateStaffNote')->middleware('can:update:user');
        Route::delete('{noteId}', 'AdminUserController@deleteStaffNote')->middleware('can:update:user');
    });

    Route::group(['prefix' => 'direct-debits'], function () {
        Route::post('/welcome-email', 'AdminDirectDebitController@resendWelcomeEmail')
            ->middleware('can:list:direct-debit-signups');

        Route::get('/signups', 'AdminDirectDebitController@getDirectDebitSignups')
            ->middleware('can:list:direct-debit-signups');
        Route::post('/signups', 'AdminDirectDebitController@createDirectDebitSignup')
            ->middleware('can:create:direct-debit-signups');

        Route::post('/cancellations/{id}/discard', 'AdminDirectDebitController@discardDirectDebitCancellation')
            ->middleware('can:manage:direct-debits');

        Route::get('/', 'AdminDirectDebitController@find')->middleware('can:manage:direct-debits');
        Route::get('/{id}', 'AdminDirectDebitController@findOneById')->middleware('can:manage:direct-debits');
        Route::post('/{id}/cancel', 'AdminDirectDebitController@cancelDirectDebit')
            ->middleware('can:manage:direct-debits');
        Route::post('/{id}/advance-cancellation', 'AdminDirectDebitController@setDirectDebitAdvanceCancellation')
            ->middleware('can:manage:direct-debits');
    });

    Route::group(['prefix' => 'transformations'], function () {
        Route::get('/', 'AdminTransformationController@getStories')->middleware('can:view:transformations');
        Route::get('/{id}', 'AdminTransformationController@getStory')->middleware('can:view:transformations');
        Route::delete('/{id}', 'AdminTransformationController@deleteStory')->middleware('can:delete:transformations');
    });

    Route::group(['prefix' => 'videos'], function () {
        Route::get('/', 'AdminVideoController@getVideos')->middleware('can:view:videos');
        Route::get('/{id}', 'AdminVideoController@getVideo')->middleware('can:view:videos');
        Route::get('/tags', 'AdminVideoController@tags')->middleware('can:view:videos');
        Route::post('/', 'AdminVideoController@submitVideo')->middleware('can:manage:videos');
        Route::put('edit/{id}', 'AdminVideoController@editVideo')->middleware('can:manage:videos');
        Route::post('edit/{id}/thumbnail', 'AdminVideoController@editThumbnail')->middleware('can:manage:videos');
        Route::delete('/{id}', 'AdminVideoController@deleteVideo')->middleware('can:manage:videos');
    });

    Route::group(['prefix' => 'competitions'], function () {
        Route::get('/', 'AdminCompetitionController@index')->middleware('can:view:competitions');
        Route::post('/', 'AdminCompetitionController@create')->middleware('can:view:competitions');
        Route::get('/{id}', 'AdminCompetitionController@show')->middleware('can:view:competitions');
        Route::put('edit/{id}', 'AdminCompetitionController@editCompetition')->middleware('can:manage:competitions');
        //images
        Route::post('edit/{id}/desktop', 'AdminCompetitionController@editImage')->middleware('can:manage:competitions');
        Route::post('edit/{id}/mobile', 'AdminCompetitionController@editImage')->middleware('can:manage:competitions');
        Route::post('edit/{id}/app', 'AdminCompetitionController@editImage')->middleware('can:manage:competitions');
        //delete
        Route::delete('/{id}', 'AdminCompetitionController@deleteCompetition')->middleware('can:manage:competitions');

        Route::get('/entries/{id}', 'AdminCompetitionController@getCompetitionEntries')
            ->middleware('can:manage:competitions');

        Route::group(['prefix' => 'competition-entries'], function () {
            Route::get('/{id}', 'AdminCompetitionController@showEntry')->middleware('can:manage:competitions');
            Route::post('/{id}/suspend', 'AdminCompetitionController@suspendEntry')
                ->middleware('can:manage:competitions');
            Route::post('/{id}/unsuspend', 'AdminCompetitionController@unsuspendEntry')
                ->middleware('can:manage:competitions');
            Route::post('/{id}/restore', 'AdminCompetitionController@restoreEntry')
                ->middleware('can:manage:competitions');
            Route::post('/{id}/winner', 'AdminCompetitionController@markAsWinner')
                ->middleware('can:manage:competitions');
            Route::delete('/{id}', 'AdminCompetitionController@deleteEntry')
                ->middleware('can:manage:competitions');
        });
    });
});

Route::group([
    'prefix' => 'api/1.0',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['api']
], function () {
    Route::get('/progress/pictures/{id}/download', 'AdminUserController@downloadProgressPicture')
        ->name('download-progress-picture');
});

// OLD ENDPOINTS

// Test route without auth
Route::group([
    'prefix' => 'admin',
    'module' => 'Admin',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['web', 'admin'],
], function () {

    // Static
    Route::get('/home', 'HomeController@index');

    // User Module
    Route::group(['prefix' => 'users'], function () {
        // Get
        Route::get('/', 'UserController@index')->middleware('can:list:users');
        Route::post('/get', 'UserController@get')->middleware('can:list:users');

        // Validate
        Route::get('/validate', 'UserController@getValidate')->middleware('can:add:user');

        // Add/ Edit
        Route::get('/add', 'UserController@create')->middleware('can:add:user');
        Route::get('/edit/{id}', 'UserController@edit')->middleware('can:view:user');
        Route::post('/store', 'UserController@storeNew')->middleware('can:add:user');
        Route::post('/store/{id}', 'UserController@store')->middleware('can:update:user');

        // Delete
        Route::get('/delete/{id}', 'UserController@delete')->middleware('can:delete:user');
        Route::get('/purge/{id}', 'UserController@purge')->middleware('can:purge:user');

        // Restore
        Route::get('/restore/{id}', 'UserController@restore')->middleware('can:restore:user');

        // Unlink MFP
        Route::get('/unlink-mfp/{id}', 'UserController@unlinkMfp')->middleware('can:unlink-mfp:user');

        // Progress pictures
        Route::get('/progress/delete/{id}', 'UserController@deleteProgressPicture')
            ->middleware('can:delete:user-progress-picture');
        Route::get('/progress/{id}', 'UserController@progress')->middleware('can:view:user-progress-pictures');
    });

    // User Module
    Route::group(['prefix' => 'users'], function () {
        // Get
        Route::get('/', 'UserController@index')->middleware('can:list:users');
    });

    // Activity Module
    Route::get('/user/{id}/activity/{type}', '\Rhf\Modules\Activity\Controllers\ActivityController@getUserLog');

    // Content Module
    Route::group(['prefix' => 'content'], function () {
        Route::get('/', 'ContentController@index')->middleware('can:list:content');
        Route::post('/get', 'ContentController@get')->middleware('can:list:content');

        // Facebook routes
        Route::group(['prefix' => 'facebook', 'middleware' => 'can:view:facebook-content'], function () {
            Route::get('/login', 'FacebookAuthController@authRedirectToFacebookProvider');

            Route::get('/get', 'FacebookContentController@get');
            Route::get('/video/{id}', '\Rhf\Modules\Admin\Controllers\FacebookContentController@getVideoUrl');
            Route::get('/{id}/add', 'FacebookContentController@add');
            Route::get('/{id}/remove', 'FacebookContentController@remove');
            Route::get('/group/{id}/add', 'FacebookContentController@add');
            Route::get('/group/{id}/remove', 'FacebookContentController@remove');
            Route::get('/', 'FacebookContentController@index');

            // Test routes
            Route::get('/deauthorise', 'FacebookAuthController@deauthoriseTestUsers');
        });

        // Local routes
        Route::get('/add', 'ContentController@create')->middleware('can:add:content');
        Route::get('/edit/{id}', 'ContentController@edit')->middleware('can:view:content');
        Route::post('/store/{id?}', 'ContentController@store')->middleware('can:update:content');

        Route::get('/delete/{id}', 'ContentController@delete')->middleware('can:delete:content');
    });

    // Content.Category Module
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'ContentController@indexCategories')->middleware('can:list:categories');
        Route::post('/get', 'ContentController@getCategories')->middleware('can:list:categories');
        Route::get('/add', 'ContentController@createCategory')->middleware('can:add:category');
        Route::get('/edit/{id}', 'ContentController@editCategory')->middleware('can:view:category');
        Route::get('/delete/{id}', 'ContentController@deleteCategory')->middleware('can:delete:category');
        Route::post('/store', 'ContentController@storeNewCategory')->middleware('can:add:category');
        Route::post('/store/{id}', 'ContentController@storeCategory')->middleware('can:update:category');
    });

    // Recipe Module
    Route::group(['prefix' => 'recipes'], function () {
        Route::get('/', 'RecipeController@index')->middleware('can:list:recipes');
        Route::get('/get', 'RecipeController@get')->middleware('can:list:recipes');
        Route::get('/add', 'RecipeController@create')->middleware('can:add:recipe');
        Route::get('/edit/{id}', 'RecipeController@edit')->middleware('can:view:recipe');
        Route::get('/delete/{id}', 'RecipeController@delete')->middleware('can:delete:recipe');
        Route::post('/store', 'RecipeController@store')->middleware('can:add:recipe');
        Route::put('/store/{id}', 'RecipeController@update')->middleware('can:update:recipe');
    });
});

Route::group([
    'prefix' => 'api/1.0/admin/features',
    'module' => 'Admin',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/', 'AdminFeatureController@features')->middleware('can:read:features');
    Route::post('/{id}/toggle', 'AdminFeatureController@toggle')->middleware('can:manage:features');
});


Route::group([
    'prefix' => 'api/1.0/admin/shopify',
    'module' => 'Admin',
    'namespace' => 'Rhf\Modules\Admin\Controllers',
    'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/', 'AdminShopifyController@index');
    Route::get('/{id}', 'AdminShopifyController@getPromotedProduct');
    Route::patch('/{id}/active', 'AdminShopifyController@toggleActivity');
    Route::patch('/{id}/website', 'AdminShopifyController@toggleWebsiteOnly');
    Route::post('/', 'AdminShopifyController@createPromotedProduct');
    // Edit Routes
    Route::put('/edit/{id}', 'AdminShopifyController@updatePromotedProduct');
    Route::post('/edit/{id}/website', 'AdminShopifyController@editImage');
    Route::post('/edit/{id}/mobile', 'AdminShopifyController@editImage');
    // Delete Routes
    Route::delete('/{id}', 'AdminShopifyController@deletePromotedProduct');
});
