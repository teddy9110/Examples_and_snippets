<?php

namespace Tests\Unit\App\Support\UserProfileHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfile;
use App\Models\UserProfileLearningProvider;
use App\Models\UserProfileQualification;
use App\Models\UserProfileSubject;
use App\Support\UserProfileHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class DeleteUserProfileTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        
        $this->userId = 1;
    }
    
    /**
     * Check throws database exception if any write fails
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenDeleteFails(): void
    {
        $this->expectException(DatabaseException::class);

        $userProfileModelMock = Mockery::mock('alias:' . UserProfile::class)->makePartial();
        $learningProviderModelMock = Mockery::mock('alias:' . UserProfileLearningProvider::class)->makePartial();

        $userProfileModelMock->shouldReceive('with')
            ->once()
            ->with(['learning_providers', 'qualifications', 'subjects'])
            ->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('learning_providers')->once()->andReturn($learningProviderModelMock);
        
        $learningProviderModelMock->shouldReceive('delete')
            ->once()
            ->andThrow(new PDOException());

        UserProfileHelper::deleteUserProfile($this->userId);
    }

    /** 
     * Check profile is deleted
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testDeleteIsCalledIfProfileExists(): void
    {
        $userProfileModelMock = Mockery::mock('alias:' . UserProfile::class)->makePartial();
        $learningProviderModelMock = Mockery::mock('alias:' . UserProfileLearningProvider::class)->makePartial();
        $qualificationsModelMock = Mockery::mock('alias:' . UserProfileQualifications::class)->makePartial();
        $subjectModelMock = Mockery::mock('alias:' . UserProfileSubject::class)->makePartial();
        
        $userProfileModelMock->shouldReceive('with')
            ->once()
            ->with(['learning_providers', 'qualifications', 'subjects'])
            ->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('delete')->once();
        
        $userProfileModelMock->shouldReceive('learning_providers')->once()->andReturn($learningProviderModelMock);
        $learningProviderModelMock->shouldReceive('delete')->once();

        $userProfileModelMock->shouldReceive('qualifications')->once()->andReturn($qualificationsModelMock);
        $qualificationsModelMock->shouldReceive('delete')->once();

        $userProfileModelMock->shouldReceive('subjects')->once()->andReturn($subjectModelMock);
        $subjectModelMock->shouldReceive('delete')->once();

        UserProfileHelper::deleteUserProfile($this->userId);
    }
}
