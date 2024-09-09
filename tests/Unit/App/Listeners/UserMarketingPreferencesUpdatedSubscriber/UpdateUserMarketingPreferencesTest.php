<?php

namespace Tests\Unit\App\Listeners\UserMarketingPreferencesUpdatedSubscriber;

use App\Events\Subscribed\UserMarketingPreferencesUpdated;
use App\Jobs\UpdateUserMarketingPreferencesJob;
use App\Listeners\UserMarketingPreferencesUpdatedSubscriber;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateUserMarketingPreferencesTest extends TestCase
{
    /**
     * Ensure the event pushes a job to update the users marketing preferences
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testDispatchesUpdateUserMarketingPreferencesJob(): void
    {
        Queue::fake();

        $subscribe = new UserMarketingPreferencesUpdatedSubscriber();
        $subscribe->updateUserMarketingPreferences(new UserMarketingPreferencesUpdated([
            'detail' => [
                'user_id' => 1,
                'marketing_preferences' => [],
                'opted_out_date' => '2021-01-01 00:00:00'
            ]
        ]));

        Queue::assertPushed(UpdateUserMarketingPreferencesJob::class);
    }

    /**
     * Ensure method catches exceptions to log the error, but re-throws the error
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLogsErrorAndReThrows(): void
    {
        $this->expectException(Exception::class);

        $exception = new Exception('Test Error');
        
        $job = Mockery::mock('alias:' . UpdateUserMarketingPreferencesJob::class)->makePartial();
        $job->shouldReceive('dispatch')
            ->once()
            ->andThrow($exception);

        Log::shouldReceive('error')->once()->with('Test Error');

        $subscribe = new UserMarketingPreferencesUpdatedSubscriber();
        $subscribe->updateUserMarketingPreferences(new UserMarketingPreferencesUpdated([
            'detail' => [
                'user_id' => 1,
                'marketing_preferences' => [],
                'opted_out_date' => '2021-01-01 00:00:00'
            ]
        ]));
    }
}
