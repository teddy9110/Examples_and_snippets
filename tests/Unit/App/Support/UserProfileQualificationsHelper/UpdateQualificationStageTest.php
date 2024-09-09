<?php

namespace Tests\Unit\App\Support\UserProfileQualificationsHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileQualification;
use App\Support\UserProfileQualificationsHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateQualificationStageTest extends TestCase
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
     * @var string
     */
    protected $stage;

    /**
     * @var array
     */
    protected $data;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->stage = 'current';
        $this->userId = 1;
        $this->id = 1;
        $this->qualification_id = 2;
        $this->end_year = '2024';
        $this->data = [
            "start_year" => '2025',
            "end_year" => $this->end_year,
            "previous" => false,
            "current" => true,
            "future" => false,
        ];
    }

    /**
    * check if the method is called with the correct parameters
    *
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    *
    * @throws \ReflectionException
    */
    public function testReplaceUsersQualificationsForStageSuccess()
    {
        $qualifications = [
            ['qualification_id' => 1, 'name' => 'Test Qualification 1'],
            ['qualification_id' => 2, 'name' => 'Test Qualification 2'],
        ];

        // Mock UserProfileQualification
        $mock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $mock->shouldReceive('updateOrCreate')->twice()->andReturn(true);
        $mock->shouldReceive('where')->once()->andReturnSelf();
        $mock->shouldReceive('whereNotIn')->once()->andReturnSelf();
        $mock->shouldReceive('where')->once()->andReturnSelf();
        $mock->shouldReceive('count')->once()->andReturn(0);
        $mock->shouldNotReceive('delete');
        // Call the method
        UserProfileQualificationsHelper::replaceUsersQualificationsForStage($this->userId, $this->stage, $qualifications);

    }
    
    /**
    * Check throws database exception if any write fails
    *
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    *
    * @throws \ReflectionException
    */
    public function testReplaceUsersQualificationsForStageException()
    {
        $this->expectException(DatabaseException::class);

        $qualifications = [
            ['qualification_id' => 1, 'name' => 'Test Qualification 1'],
        ];

        // Mock UserProfileQualification
        $mock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $mock->shouldReceive('updateOrCreate')->once()->andThrow(new PDOException());

        // Call the method
        UserProfileQualificationsHelper::replaceUsersQualificationsForStage($this->userId, $this->stage, $qualifications);

    }
    /**
     * Check deleates old qualifications 
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testReplaceUsersQualificationsForStageDeleteOldQualifications()
    {
        $qualifications = [
            ['qualification_id' => 1, 'name' => 'Test Qualification 1'],
        ];

        // Mock UserProfileQualification
        $mock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $mock->shouldReceive('updateOrCreate')->once()->andReturn(true);
        $mock->shouldReceive('where')->once()->andReturnSelf();
        $mock->shouldReceive('whereNotIn')->once()->andReturnSelf();
        $mock->shouldReceive('where')->once()->andReturnSelf();
        $mock->shouldReceive('count')->once()->andReturn(1);
        $mock->shouldReceive('delete')->once();

        // Call the method
        UserProfileQualificationsHelper::replaceUsersQualificationsForStage($this->userId, $this->stage, $qualifications);
    }
    
}
