<?php

namespace Tests\Unit\App\Support\UserProfileMarketingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserMarketingPreference;
use App\Support\UserProfileMarketingPreferencesHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class GetMarketingPreferenceTest extends TestCase
{
    /**
     * Ensure that an exception is thrown when the database errors
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function testThrowsExceptionWhenDatabaseErrors(): void
    {
        // We are expecting a database exception
        $this->expectException(DatabaseException::class);

        $userProfileModelMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();
        $userProfileModelMock->shouldReceive('where')->once()->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('get')->once()->andThrow(PDOException::class);

        UserProfileMarketingPreferencesHelper::getUserMarketingPreferences(1);
    }

    /**
     * Ensure the profile from the database is returned
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testReturnsProfileModel(): void
    {
        $userProfileModelMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();
        $userProfileModelMock->shouldReceive('where')->once()->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('get')->once()->andReturn($userProfileModelMock);

        $profile = UserProfileMarketingPreferencesHelper::getUserMarketingPreferences(1);
        
        self::assertInstanceOf(UserMarketingPreference::class, $profile);
    }
}
