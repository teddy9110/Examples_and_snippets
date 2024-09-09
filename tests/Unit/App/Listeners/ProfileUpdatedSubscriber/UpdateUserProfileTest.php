<?php

namespace Tests\Unit\App\Listeners\ProfileUpdatedSubscriber;

use App\Events\Subscribed\LeadCreated;
use App\Events\Subscribed\DPCProfileUpdated;
use App\Listeners\ProfileUpdatedSubscriber;
use App\Support\UserProfileHelper;
use App\Jobs\UpdateProfileJob;
use Exception;
use Mockery;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UpdateUserProfileTest extends TestCase
{
    /**
     * Ensure the event updates the user profile
     * 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testLeadCreatedEventUpdatesUserProfile(): void
    {
        Queue::fake();

        $ProfileUpdatedSubscriber = new ProfileUpdatedSubscriber();
        $ProfileUpdatedSubscriber->updateUserProfile(new LeadCreated([
            'detail' => [
                'lead_info' => [
                    'user_id' => 1
                ],
                'user_info' => [
                    'first_name' => 'Test'
                ]
            ]
        ]));

        Queue::assertPushed(UpdateProfileJob::class);
    }


    /**
     * Ensure the event updates the user profile
     * 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testProfileUpdatedEventUpdatesUserProfile(): void
    {
        Queue::fake();

        $ProfileUpdatedSubscriber = new ProfileUpdatedSubscriber();
        $ProfileUpdatedSubscriber->updateUserProfile(new DPCProfileUpdated([
            'detail' => [
                'user_id' => 1,
                'first_name' => 'Test'
            ]
        ]));

        Queue::assertPushed(UpdateProfileJob::class);
    }

    /**
     * Ensure we dont try and update a profile if there is no user id
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUpdateUserProfileDoesntUpdateProfileWhenNoUserId(): void
    {
        Queue::fake();

        $ProfileUpdatedSubscriber = new ProfileUpdatedSubscriber();
        $ProfileUpdatedSubscriber->updateUserProfile(new LeadCreated([
            'detail' => [
                'lead_info' => [
                    'user_id' => null
                ],
                'user_info' => [
                    'first_name' => 'Test'
                ]
            ]
        ]));

        Queue::assertNotPushed(UpdateProfileJob::class);
    }
}
