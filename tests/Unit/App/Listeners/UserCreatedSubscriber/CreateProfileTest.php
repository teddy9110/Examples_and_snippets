<?php

namespace Tests\Unit\App\Listeners\UserCreatedSubscriber;

use Carbon\Carbon;
use App\Events\Subscribed\UserCreated;
use App\Jobs\SyncGraphJob;
use App\Listeners\UserCreatedSubscriber;
use App\Models\UserProfile;
use App\Support\UserProfileHelper;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateProfileTest extends TestCase
{

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Ensure the event deletes the user profile
     * 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testSetsOptedOutToNullIfUserIsOptedIn(): void
    {
        Queue::fake();

        $userProfileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class)->makePartial();
        $userProfileHelperMock->shouldReceive('updateOrCreateUserProfile')->once()->with(1, ['email_opted_out_at' => null])->andReturn(new UserProfile());

        $userCreatedSubscriber = new UserCreatedSubscriber();
        $userCreatedSubscriber->createProfile(new UserCreated([
            'detail' => [
                'user' => [
                    'id' => 1,
                ],
                'opted_in' => true
            ]
        ]));

        Queue::assertPushed(SyncGraphJob::class);
    }

    /**
     * Ensure the event deletes the user profile
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testSetsOptedOutToCurrentDateIfUserIsOptedOut(): void
    {
        $this->freezeTime();
        $currentTime = Carbon::now();

        Queue::fake();

        $userProfileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class)->makePartial();
        $userProfileHelperMock->shouldReceive('updateOrCreateUserProfile')->once()->with(1, ['email_opted_out_at' => $currentTime])->andReturn(new UserProfile());

        $userCreatedSubscriber = new UserCreatedSubscriber();
        $userCreatedSubscriber->createProfile(new UserCreated([
            'detail' => [
                'user' => [
                    'id' => 1,
                ],
                'opted_in' => false
            ]
        ]));

        Queue::assertPushed(SyncGraphJob::class);
    }

    /**
     * Ensure method catches exceptions
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCreateProfileCatchesException(): void
    {
        $exception = new Exception();
        
        $userProfileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class)->makePartial();
        $userProfileHelperMock->shouldReceive('updateOrCreateUserProfile')
            ->once()
            ->with(1, ['email_opted_out_at' => null])
            ->andThrow($exception);

        Log::shouldReceive('error')->once()->with($exception);

        $userCreatedSubscriber = new UserCreatedSubscriber();
        $userCreatedSubscriber->createProfile(new UserCreated([
            'detail' => [
                'user' => [
                    'id' => 1,
                ],
                'opted_in' => true
            ]
        ]));
    }
}
