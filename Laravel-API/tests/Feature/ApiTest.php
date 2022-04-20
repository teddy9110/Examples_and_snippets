<?php

namespace Tests\Feature;

use AppReviewTopicSeeder;
use Carbon\Carbon;
use Database\seeders\ExerciseLevelSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\User\Models\AppReviewTopic;
use Rhf\Modules\User\Models\UserPreferences;
use Database\Seeders\ExerciseFrequencySeeder;
use Database\Seeders\ExerciseLocationSeeder;
use Database\Seeders\ExerciseLevelSeeder;
use Rhf\Modules\Workout\Models\WorkoutPreference;
use Tests\TestCase;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserAppStoreReview;

class ApiTest extends TestCase
{
    use DatabaseTransactions;

    //set api headers
    public $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'api-version' => '20211230'
    ];

    public $user;
    public $token;
    public $level;
    public $location;
    public $freq;

    //api route / version
    public $route = '/api/';
    public $version = '1.0';

    public function testHealthCheck()
    {
        $response = $this->withHeaders($this->headers)
            ->json('GET', $this->route . $this->version);

        $response->assertOk();
    }

    public function setupUser()
    {
        $this->user = User::create([
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'active' => 0,
            'paid' => 1,
            'expiry_date' => now()->addUnit('year', 1)
        ]);
        return $this->user;
    }
    public function setUserToDefaultName()
    {
        $this->user->first_name = 'test';
        $this->user->surname = 'user';
        $this->user->save();
    }

    public function setupUserPreferences()
    {
        UserPreferences::factory()->create(
            [
                'user_id' => $this->user->id,
                'user_role' => $this->user->role_id,
            ]
        );
        WorkoutPreference::factory()->create(
            [
                'user_id' => $this->user->id,
                'schedule' => null,
                'exercise_location_id' => $this->location,
                'exercise_frequency_id' => $this->freq,
            ]
        );
    }

    public function setUserActive(): void
    {
        //activates user
        $this->user->active = 1;
        $this->user->save();
    }

    public function setUserInactive(): void
    {
        //activates user
        $this->user->active = 0;
        $this->user->save();
    }

    public function setUserPaid(): void
    {
        //activates user
        $this->user->paid = 1;
        $this->user->save();
    }

    public function setUserUnpaid(): void
    {
        //activates user
        $this->user->paid = 0;
        $this->user->save();
    }

    public function setUserEligible(): void
    {
        $this->user->setPreference('start_weight', 250);

        Activity::factory()
            ->modifier('weight', 200, 200)
            ->create([
                'user_id' => $this->user->id,
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            ]);

        UserAppStoreReview::factory()->create(
            [
                'user_id' => $this->user->id,
                'next_review_request' => Carbon::now()->addDays(150)->startOfDay(),
            ]
        );
        $this->user->save();
    }

    protected function setUserIneligible(): void
    {
        $this->user->setPreference('start_weight', 250);

        Activity::factory()
            ->modifier('weight', 200, 200)
            ->create([
                'user_id' => $this->user->id,
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            ]);

        UserAppStoreReview::factory()->create(
            [
                'user_id' => $this->user->id,
                'next_review_request' => Carbon::now()->subDays(10)->startOfDay(),
            ]
        );
        $this->user->save();
    }

    protected function setupTopics(): void
    {
        if (AppReviewTopic::all()->isEmpty()) {
            $topicsSeeder = new AppReviewTopicSeeder();
            $topicsSeeder->run();
        }
    }

    public function setupExercise(): void
    {
        if (ExerciseFrequency::all()->isEmpty()) {
            $exerciseFreqSeeder = new ExerciseFrequencySeeder();
            $exerciseFreqSeeder->run();
        }

        if (ExerciseLocation::all()->isEmpty()) {
            $exerciseLocationSeeder = new ExerciseLocationSeeder();
            $exerciseLocationSeeder->run();
        }

        $this->location = ExerciseLocation::where('slug', array_rand(array_flip(['home', 'gym'])))->first()->id;
        $this->freq = ExerciseFrequency::where('slug', array_rand(array_flip(['0', '3', '6', '5'])))->first()->id;
    }

    public function getUserResponse(string $method, string $route)
    {
        return $this->actingAs($this->user, 'api')
            ->withHeaders($this->headers)
            ->json($method, $this->route . $this->version . $route);
    }

    public function postUserResponse(
        string $method,
        string $route,
        array $data
    ) {
        return $this->actingAs($this->user, 'api')
            ->withHeaders($this->headers)
            ->json($method, $this->route . $this->version . $route, $data);
    }

    public function getDatesBetweenPeriods($startDate, $endDate)
    {
        $dateRange = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            new \DateTime($endDate . '+1 day')
        );

        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        return $dates;
    }

    public function createRangeOfActivities(int $range, Carbon $date)
    {
        $activityType = [
            'steps', 'weight', 'water', 'exercise', 'protein', 'fiber', 'calories'
        ];

        foreach (array_slice($activityType, 0, $range) as $activity) {
            $value = $this->getUserPreferences($activity);

            Activity::factory()
                ->modifier($activity, $value, $value)
                ->create([
                    'user_id' => $this->user->id,
                    'date' => $date->format('Y-m-d'),
                ]);
        }
    }

    public function getUserpreferences(string $activity)
    {
        if ($activity === 'calories') {
            return $this->user->getPreference('daily_calorie_goal');
        } elseif ($activity === 'exercise' || $activity === 'weight') {
            return 0;
        } elseif ($activity === 'steps') {
            return $this->user->getPreference('daily_step_goal');
        } else {
            return $this->user->getPreference('daily_' . $activity . '_goal');
        }
    }

    public function unsetUserMFP(): void
    {
        $preferences = UserPreferences::where('user_id', $this->user->id)->first();
        $preferences->update([
            'mfp_access_token' => null,
            'mfp_refresh_token' => null,
            'mfp_token_expires_at' => null,
            'mfp_user_id' => null,
            'mfp_authentication_code' => null,
        ]);
        $preferences->save();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->user);
    }
}
