<?php

namespace Tests\Unit\App\Support\UserProfileHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfile;
use App\Models\UserProfileQualification;
use App\Support\UserProfileHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateOrCreateUserProfileTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var array
     */
    protected $userInfo;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        
        $this->userId = 1;
        $this->userInfo = [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'mobile' => '07777777777',
            'post_code' => 'ABC 123',
            'country' => 'UK'
        ];
    }
    
    /**
     * Check throws database exception if any write fails
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenUpdateFails(): void
    {
        $this->expectException(DatabaseException::class);
        
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('update')
            ->once()
            ->with($this->userInfo)
            ->andThrow(new PDOException());

        UserProfileHelper::updateOrCreateUserProfile($this->userId, $this->userInfo);
    }

    /**
     * Check update is called on the model if the profile exists
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateIsCalledIfProfileExists(): void
    {
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('update')
            ->once()
            ->with($this->userInfo)
            ->andReturn($userProfileModelMock);

        UserProfileHelper::updateOrCreateUserProfile($this->userId, $this->userInfo);
    }

    /**
     * Check create is called if the profile does not exist
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testCreateIsCalledIfProfileDoesNotExist()
    {
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn(null);
        $userProfileModelMock->shouldNotReceive('update');
        $userProfileModelMock->shouldReceive('create')
            ->once()
            ->with(array_merge(['user_id' => $this->userId], $this->userInfo))
            ->andReturn($userProfileModelMock);

        UserProfileHelper::updateOrCreateUserProfile($this->userId, $this->userInfo);
    }

    /**
     * Check we update the qualifications associated with a user's profile if provided
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testQualificationsAreUpdated(): void
    {
        $qualifications = [1, 2];
        
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('with')->once()->with(['learning_providers', 'qualifications', 'subjects'])->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('find')->once()->with(1)->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('update')
            ->once()
            ->with($this->userInfo)
            ->andReturn($userProfileModelMock);
        
        $UserProfileQualificationMock = Mockery::mock('alias:'.UserProfileQualification::class)->makePartial();
        $UserProfileQualificationMock->shouldReceive('firstOrCreate')
            ->with(['user_id' => $this->userId, 'qualification_id' => $qualifications[0]])
            ->andReturn($UserProfileQualificationMock);
        $UserProfileQualificationMock->shouldReceive('firstOrCreate')
            ->with(['user_id' => $this->userId, 'qualification_id' => $qualifications[1]])
            ->andReturn($UserProfileQualificationMock);

        $userProfileModelMock->shouldReceive('qualifications')->once()->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('whereNotIn')->once()->andReturn($userProfileModelMock);
        $userProfileModelMock->shouldReceive('delete')->once();

        UserProfileHelper::updateOrCreateUserProfile($this->userId, $this->userInfo, $qualifications);
    }
}
