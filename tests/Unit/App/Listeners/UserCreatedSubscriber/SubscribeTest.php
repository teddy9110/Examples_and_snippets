<?php

namespace Tests\Unit\App\Listeners\UserCreatedSubscriber;

use Illuminate\Events\Dispatcher;
use App\Events\Subscribed\UserCreated;
use App\Listeners\UserCreatedSubscriber;
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
        $userCreatedSubscriber = new UserCreatedSubscriber();
        $subscribed = $userCreatedSubscriber->subscribe(new Dispatcher());
        self::assertCount(1, $subscribed);
        self::assertEquals('createProfile', $subscribed[UserCreated::class]);
    }
}
