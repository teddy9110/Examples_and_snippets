<?php

namespace Tests\Unit\App\Support\UserProfileMarketingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserMarketingPreference;
use App\Models\MarketingPreference;
use App\Support\UserProfileMarketingPreferencesHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    /**
     * Ensure we call updateOrCreateUserProfileMarketingPreference with the right params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testCallsUpdateOrCreateUserProfileMarketingPreference(): void
    {
        $marketingPreferenceMock =  Mockery::mock('alias:' . MarketingPreference::class)->makePartial();
        $marketingPreferenceMock->id = 1;
        $marketingPreferenceMock->code = 'B2C_NEWSLETTER';
        $marketingPreferenceMock->name = 'Newsletter';
        $marketingPreferenceMock->default_marketing_frequency = 'WEEKLY';
        $marketingPreferenceMock->shouldReceive('where')->once()->andReturnSelf();
        $marketingPreferenceMock->shouldReceive('firstOrFail')->once()->andReturnSelf();

        $userProfileMarketingPreferenceMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();
        $userProfileMarketingPreferenceMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => 2,
                    'marketing_preference_id' => 1
                ],
                [
                    'user_id' => 2,
                    'marketing_preference_id' => 1,
                    'frequency' => 'NEVER',
                ]
            )->andReturn($userProfileMarketingPreferenceMock);


        UserProfileMarketingPreferencesHelper::unsubscribe(2, 'B2C_NEWSLETTER');
    }
}
