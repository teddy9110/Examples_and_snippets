<?php

namespace Tests\Unit\App\Listeners\UserDeletedSubscriber;

use App\Events\Subscribed\UserDeleted;
use App\Jobs\SyncGraphJob;
use App\Listeners\UserDeletedSubscriber;
use App\Models\UserProfile;
use App\Support\UserProfileHelper;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteUserProfileTest extends TestCase
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
    public function testEventDeletesUserProfile(): void
    {
        Queue::fake();

        $userProfileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class)->makePartial();
        $userProfileHelperMock->shouldReceive('deleteUserProfile')->once()->with(1);
        $userProfileHelperMock->shouldReceive('getProfileByUserId')->once()->with(1)->andReturn(new UserProfile());

        $userDeletedSubscriber = new UserDeletedSubscriber();
        $userDeletedSubscriber->deleteUserProfile(new UserDeleted([
            'detail' => [
                'user_id' => 1
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
    public function testDeleteUserProfileCatchesException(): void
    {
        $exception = new Exception();
        
        $userProfileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class)->makePartial();
        $userProfileHelperMock->shouldReceive('deleteUserProfile')
            ->once()
            ->with(1)
            ->andThrow($exception);

        Log::shouldReceive('error')->once()->with($exception);

        $userDeletedSubscriber = new UserDeletedSubscriber();
        $userDeletedSubscriber->deleteUserProfile(new UserDeleted([
            'detail' => [
                'user_id' => 1
            ]
        ]));
    }
}
