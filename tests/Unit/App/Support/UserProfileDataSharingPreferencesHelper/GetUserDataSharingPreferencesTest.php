<?php

namespace Tests\Unit\App\Support\UserProfileDataSharingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileDataSharingPreference;
use App\Support\UserProfileDataSharingPreferencesHelper;
use Mockery;
use Tests\TestCase;

class GetUserDataSharingPreferencesTest extends TestCase
{
    /**
     * Ensure calls getUserDataSharingPreferences with right params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testCallsGetUserDataSharingPreferences(): void {
        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('getUserProfileDataSharingPreferences')->once()->with(1)->andReturn(['study_level']);

        $dataSharingPreferences = UserProfileDataSharingPreferencesHelper::getUserDataSharingPreferences(1);

        self::assertEquals(['study_level'], $dataSharingPreferences);
    }
    
    /**
     * Ensure getUserDataSharingPreferences handles database exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testGetUserDataSharingPreferencesHandlesDatabaseException(): void {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Error getting User Data Sharing Preference from database');

        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('getUserProfileDataSharingPreferences')->once()->with(1)->andThrow(new \Exception());

        $dataSharingPreferences = UserProfileDataSharingPreferencesHelper::getUserDataSharingPreferences(1);

        self::assertEquals(['study_level'], $dataSharingPreferences);
    }
}
