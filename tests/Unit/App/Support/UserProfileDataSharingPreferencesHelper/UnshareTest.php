<?php

namespace Tests\Unit\App\Support\UserProfileDataSharingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileDataSharingPreference;
use App\Support\UserProfileDataSharingPreferencesHelper;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class UnshareTest extends TestCase
{
    /**
     * Ensure calls unshare with right params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testCallsGetUserDataSharingPreferences(): void {
        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('where->where->first')->once()->andReturn($dataPreferencesMock);
        $dataPreferencesMock->shouldReceive('delete')->once()->andReturn();

        UserProfileDataSharingPreferencesHelper::unshare(1, 'study_level');
    }
    
    /**
     * Ensure unshare handles database exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testGetUserDataSharingPreferencesHandlesDatabaseException(): void {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Error removing Data Sharing Preference from database');

        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('where->where->first')->once()->andReturn($dataPreferencesMock);
        $dataPreferencesMock->shouldReceive('delete')->once()->andThrow(new \Exception());

        $dataSharingPreferences = UserProfileDataSharingPreferencesHelper::unshare(1, 'study_level');
    }
}
