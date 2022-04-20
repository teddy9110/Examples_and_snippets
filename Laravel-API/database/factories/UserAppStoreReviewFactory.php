<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Rhf\Modules\User\Models\UserAppStoreReview;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(UserAppStoreReview::class, function (Faker $faker) {
    return [
        'user_id' => '',
        'present_review_dialog' => false,
        'next_review_request' => '',
        'last_review_submitted' => null,
        'user_response' => null,
    ];
});
