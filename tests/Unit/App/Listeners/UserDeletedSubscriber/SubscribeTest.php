<?php

namespace Tests\Unit\App\Listeners\UserDeletedSubscriber;

use Illuminate\Events\Dispatcher;
use App\Events\Subscribed\UserDeleted;
use App\Listeners\UserDeletedSubscriber;
use Exception;
use Tests\TestCase;

class SubscribeTest extends TestCase
{

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Ensure the subscriber maps the right events to the right functions
     * 
     * @return void
     *
     * @throws \JsonException
     */
    public function testSubscribeDetails(): void
    {
        $userDeletedSubscriber = new UserDeletedSubscriber();
        $subscribed = $userDeletedSubscriber->subscribe(new Dispatcher());
        self::assertCount(1, $subscribed);
        self::assertEquals('deleteUserProfile', $subscribed[UserDeleted::class]);
    }
}
