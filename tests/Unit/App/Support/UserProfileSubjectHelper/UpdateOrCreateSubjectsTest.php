<?php

namespace Tests\Unit\App\Support\UserProfileSubjectHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileSubject;
use App\Support\UserProfileSubjectHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateOrCreateSubjectsTest extends TestCase
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
        $this->subjectId = 2;
        $this->subjectIds = [2];
        $this->subjectStage = 'future';
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

        $userProfileSubjectMock = Mockery::mock('alias:' . UserProfileSubject::class)->makePartial();

        $userProfileSubjectMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'subject_id' => 2,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ],
                [
                    'subject_id' => 2,
                    'user_id' => $this->userId,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ]
            )->andThrow(new PDOException());

        UserProfileSubjectHelper::updateOrCreateSubjects($this->userId, $this->subjectIds, $this->subjectStage);
    }

    /**
     * Check updateOrCreate is called on the model with the correct params
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateOrCreateIsCalledWithCorrectParams()
    {
        $userProfileSubjectMock = Mockery::mock('alias:' . UserProfileSubject::class)->makePartial();

        $userProfileSubjectMock->user_id = $this->userId;
        $userProfileSubjectMock->subject_id = 2;
        $userProfileSubjectMock->current = false;
        $userProfileSubjectMock->previous = false;
        $userProfileSubjectMock->future = true;

        $userProfileSubjectMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'subject_id' => 2,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ],
                [
                    'subject_id' => 2,
                    'user_id' => $this->userId,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ]
            )->andReturnSelf();

            $userProfileSubjectMock->shouldReceive('where')
            ->twice()->andReturnSelf();

            $userProfileSubjectMock->shouldReceive('whereNotIn')
            ->once()->andReturnSelf();

            $userProfileSubjectMock->shouldReceive('get')
            ->once()->andReturn(new Collection());

            $userProfileSubjectMock->shouldReceive('delete')->andReturnNull();

        UserProfileSubjectHelper::updateOrCreateSubjects($this->userId, $this->subjectIds, $this->subjectStage);
    }
}
