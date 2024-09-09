<?php

namespace Tests\Unit\App\Support\UserProfileQualificationsHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileQualification;
use App\Support\UserProfileQualificationsHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateUsersQualificationsTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $subjectId;

    /**
     * @var string
     */
    protected $subjectStage;

    /**
     * @var array
     */
    protected $subjectInfo;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userId = 1;
        $this->qualification_id = 2;
        $this->end_year = '2024';
    }

    /**
     * Check throws database exception if any write fails
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenUpdateOrCreateFails(): void
    {
        $this->expectException(DatabaseException::class);
        $data = [
            'qualification_id' => 2,
            'end_year' => $this->end_year,
        ];

        $userProfileQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();

        $userProfileQualificationMock->shouldReceive('where')
        ->once()->andThrow(new PDOException());

        UserProfileQualificationsHelper::updateUsersQualifications($data, $this->userId);
        
    }

    /**
     * Check update is called on the model with the correct params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */

    public function testUpdateUsersQualifications()
    {
        // Arrange
        // Mock dependencies and set up the test scenario
        $userId = 2;
        $data = [
            'qualification_id' => 2,
            'end_year' => $this->end_year,
        ];

        // Create a mock for UserProfileQualification
        $userQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        
        $userQualificationMock->shouldReceive('where')->once()->with('user_id', $userId)->andReturnSelf();
        $userQualificationMock->shouldReceive('where')->once()->with('qualification_id', $data['qualification_id'])->andReturnSelf();
        $userQualificationMock->shouldReceive('firstOrFail')->once()->andReturn($userQualificationMock);
        $userQualificationMock->shouldReceive('update')->once()->with([
            'end_year' => $this->end_year,
        ])->andReturn(true);
        $userQualificationMock->shouldReceive('save')->once();

        UserProfileQualificationsHelper::updateUsersQualifications($data, $userId);
    }
    
}
