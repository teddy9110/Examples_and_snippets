<?php

namespace Tests\Unit\App\Listeners\UserMarketingPreferencesUpdatedSubscriber;

use App\Events\Subscribed\UserEmailUnsubscribed;
use App\Jobs\UpdateProfileJob;
use App\Listeners\UserMarketingPreferencesUpdatedSubscriber;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserEmailUnsubscribedTest extends TestCase
{
    /**
     * Ensure the event pushes a job to update the users profile
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testDispatchesUpdateProfileJob(): void
    {
        Queue::fake();

        $subscribe = new UserMarketingPreferencesUpdatedSubscriber();
        $subscribe->userEmailUnsubscribed(new UserEmailUnsubscribed([
            'detail' => [
                'user_id' => 1,
                'email_opted_out_at' => '2021-01-01 00:00:00'
            ]
        ]));

        Queue::assertPushed(UpdateProfileJob::class);
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
        
        $job = Mockery::mock('alias:' . UpdateProfileJob::class)->makePartial();
        $job->shouldReceive('dispatch')
            ->once()
            ->andThrow($exception);

        Log::shouldReceive('error')->once()->with('Test Error');

        $subscribe = new UserMarketingPreferencesUpdatedSubscriber();
        $subscribe->userEmailUnsubscribed(new UserEmailUnsubscribed([
            'detail' => [
                'user_id' => 1,
                'email_opted_out_at' => '2021-01-01 00:00:00'
            ]
        ]));
    }
}
