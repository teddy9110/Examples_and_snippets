<?php

namespace Tests\Unit\App\Support\UserProfileDataSharingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileDataSharingPreference;
use App\Support\UserProfileDataSharingPreferencesHelper;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class ShareTest extends TestCase
{
    /**
     * Ensure calls share with right params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testCallsGetUserDataSharingPreferences(): void {
        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('updateOrCreate')->once()->andReturn();

        UserProfileDataSharingPreferencesHelper::share(1, 'study_level');
    }
    
    /**
     * Ensure share handles database exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testGetUserDataSharingPreferencesHandlesDatabaseException(): void {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Error updating or creating Data Sharing Preference for database');

        $dataPreferencesMock = Mockery::mock('alias:' . UserProfileDataSharingPreference::class)->makePartial();
        $dataPreferencesMock->shouldReceive('updateOrCreate')->once()->andThrow(new \Exception());

        UserProfileDataSharingPreferencesHelper::share(1, 'study_level');
    }
}
