<?php declare(strict_types=1);

namespace Tests\Unit\App\Jobs;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Jobs\UpdateUserMarketingPreferencesJob;
use App\Support\UserProfileHelper;
use App\Support\UserProfileMarketingPreferencesHelper;
use App\Events\Publishes\MarketingPreferencesUpdated;
use TSR\EventBridgeIngestion\Publisher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Mockery;
use Exception;
use App\Models\UserProfile;
use App\Exceptions\ProfileNotFoundException;

class UpdateUserMarketingPreferencesJobTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testProcessesWhenProfileCreateOrUpdateSucceeds()
    {
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        $userId = 1;
        $preferences = [
            ['code' => 'B2C_NEWSLETTER', 'value' => 'WEEKLY'],
            ['code' => 'B2B2C_BRAND_PARTNERS', 'value' => 'MONTHLY']
        ];

        $userProfile = UserProfile::factory()->count(1)->create(['user_id' => $userId])->first();

        // Mocking UserProfileHelper
        Mockery::mock('alias:' . UserProfileHelper::class)
            ->shouldReceive('updateOrCreateUserProfile')
            ->with($userId, [], [])
            ->andReturn($userProfile);

        // Mocking UserProfileMarketingPreferencesHelper
        Mockery::mock('alias:' . UserProfileMarketingPreferencesHelper::class)
            ->shouldReceive('updateOrCreateUserProfileMarketingPreference')
            ->with($userId, Mockery::on(function ($arg) use ($preferences) {
                return in_array($arg, $preferences);
            }), true)
            ->times(count($preferences));

        $job = new UpdateUserMarketingPreferencesJob($userId, $preferences, []);
        $job->handle();
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testHandlesExceptions()
    {
        $userId = 1;
        $preferences = [
            ['code' => 'B2C_NEWSLETTER', 'value' => 'WEEKLY'],
            ['code' => 'B2B2C_BRAND_PARTNERS', 'value' => 'MONTHLY']
        ];

        $userProfile = UserProfile::factory()->count(1)->create(['user_id' => $userId])->first();

        // Mocking UserProfileHelper
        Mockery::mock('alias:' . UserProfileHelper::class)
            ->shouldReceive('updateOrCreateUserProfile')
            ->with($userId, [], [])
            ->andReturn($userProfile);

        // Simulate an exception when updating preferences
        $msg = "Error updating preferences";
        $exception = new Exception($msg);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($msg);
        Mockery::mock('alias:' . UserProfileMarketingPreferencesHelper::class)
            ->shouldReceive('updateOrCreateUserProfileMarketingPreference')
            ->andThrow($exception);

        // Expect logging of the error
        Log::shouldReceive('error')
            ->with(Mockery::on(function ($arg) use ($exception) {
                return $arg == $exception;
            }));

        $job = new UpdateUserMarketingPreferencesJob($userId, $preferences, []);

        // We expect the job to handle its own failure
        $job->handle();
    }
}
