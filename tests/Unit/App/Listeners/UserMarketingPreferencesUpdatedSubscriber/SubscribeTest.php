<?php

namespace Tests\Unit\App\Listeners\UserMarketingPreferencesUpdatedSubscriber;

use Illuminate\Events\Dispatcher;
use App\Events\Subscribed\UserMarketingPreferencesUpdated;
use App\Events\Subscribed\UserEmailUnsubscribed;
use App\Listeners\UserMarketingPreferencesUpdatedSubscriber;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    /**
     * Ensure the subscriber maps the right events to the right functions
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testSubscribeDetails(): void
    {
        $userMarketingPreferencesSubscriber = new UserMarketingPreferencesUpdatedSubscriber();
        $subscribed = $userMarketingPreferencesSubscriber->subscribe(new Dispatcher());
        self::assertCount(2, $subscribed);
        self::assertEquals('updateUserMarketingPreferences', $subscribed[UserMarketingPreferencesUpdated::class]);
        self::assertEquals('userEmailUnsubscribed', $subscribed[UserEmailUnsubscribed::class]);
    }
}
