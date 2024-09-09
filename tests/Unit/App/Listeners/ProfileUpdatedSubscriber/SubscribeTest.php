<?php

namespace Tests\Unit\App\Listeners\ProfileUpdatedSubscriber;

use Tests\TestCase;
use App\Events\Subscribed\LeadCreated;
use App\Events\Subscribed\DPCProfileUpdated;
use App\Listeners\ProfileUpdatedSubscriber;

class SubscribeTest extends TestCase
{
    /**
     * Ensure the correct event mapping is returned
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testReturnsCorrectEventMapping(): void
    {
        $subscriber = new ProfileUpdatedSubscriber();
        $config = $subscriber->subscribe(new \Illuminate\Events\Dispatcher());

        self::assertEquals([
            LeadCreated::class => 'updateUserProfile',
            DPCProfileUpdated::class => 'updateUserProfile',
        ], $config);
    }
}
