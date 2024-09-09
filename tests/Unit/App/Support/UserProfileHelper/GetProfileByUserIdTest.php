<?php

namespace Tests\Unit\App\Support\UserProfileHelper;

use App\Exceptions\DatabaseException;
use App\Exceptions\ProfileNotFoundException;
use App\Models\UserProfile;
use App\Support\UserProfileHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class GetProfileByUserIdTest extends TestCase
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

        $userProfileModelMock = Mockery::mock('alias:' . UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andThrow(PDOException::class);

        UserProfileHelper::getProfileByUserId(1);
    }

    /**
     * Ensure that an exception is thrown when the user profile is not found
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function testThrowsExceptionWhenProfileNotFound(): void
    {
        // We are expecting a database exception
        $this->expectException(ProfileNotFoundException::class);

        $userProfileModelMock = Mockery::mock('alias:' . UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn(null);

        UserProfileHelper::getProfileByUserId(1);
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
        $userProfileModelMock = Mockery::mock('alias:' . UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);

        $profile = UserProfileHelper::getProfileByUserId(1);
        
        self::assertInstanceOf(UserProfile::class, $profile);
    }
}
