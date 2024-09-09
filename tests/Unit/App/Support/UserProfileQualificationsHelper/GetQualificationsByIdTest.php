<?php

namespace Tests\Unit\App\Support\UserProfileQualificationsHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileQualification;
use App\Support\UserProfileQualificationsHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class GetQualificationsByIdTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $end_year;
     
    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userId = 1;
        $this->id = 1;
        $this->end_year = '2024';
    }

    /**
     * Check throws database exception if any read fails
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

        $userQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $userQualificationMock->shouldReceive('find')->once()->andThrow(new PDOException());

        UserProfileQualificationsHelper::getQualificationById($this->id);
        
    }

    /**
     * Check get is called correctly 
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */

    public function testGetQualificationsById()
    {
        // Arrange

        // Create a mock for UserProfileQualification
        $userQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $userQualificationMock->shouldReceive('find')->once()->andReturnSelf();

        UserProfileQualificationsHelper::getQualificationById($this->id);
    }
    
}
