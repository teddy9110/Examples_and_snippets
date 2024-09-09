<?php

namespace Tests\Unit\App\Support\UserProfileHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfile;
use App\Models\UserProfileQualification;
use App\Support\UserProfileHelper;
use Mockery;
use PDOException;
use Tests\TestCase;

class CreateUserProfileTest extends TestCase
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
    public function testThrowsExceptionWhenCreateFails(): void
    {
        $this->expectException(DatabaseException::class);

        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('create')
            ->once()
            ->with(array_merge(['user_id' => $this->userId], $this->userInfo))
            ->andThrow(new PDOException());

        UserProfileHelper::createUserProfile($this->userId, $this->userInfo, []);
    }

    /**
     * Check we create the profile with the right details
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testCallsCreateWithCorrectParams(): void
    {
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('create')
            ->once()
            ->with(array_merge(['user_id' => $this->userId], $this->userInfo))
            ->andReturn($userProfileModelMock);

        UserProfileHelper::createUserProfile($this->userId, $this->userInfo, []);
    }

    /**
     * Check we create the qualifications if provided
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testQualificationsAreCreated(): void
    {
        $qualifications = [1, 2];
        
        $userProfileModelMock = Mockery::mock('alias:'.UserProfile::class)->makePartial();
        $userProfileModelMock->shouldReceive('create')
            ->once()
            ->with(array_merge(['user_id' => $this->userId], $this->userInfo))
            ->andReturn($userProfileModelMock);

        $UserProfileQualificationMock = Mockery::mock('alias:'.UserProfileQualification::class)->makePartial();
        $UserProfileQualificationMock->shouldReceive('firstOrCreate')
            ->with(['user_id' => $this->userId, 'qualification_id' => $qualifications[0]])
            ->andReturn($UserProfileQualificationMock);
        $UserProfileQualificationMock->shouldReceive('firstOrCreate')
            ->with(['user_id' => $this->userId, 'qualification_id' => $qualifications[1]])
            ->andReturn($UserProfileQualificationMock);

        UserProfileHelper::createUserProfile($this->userId, $this->userInfo, $qualifications);
    }
}
