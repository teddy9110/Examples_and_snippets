<?php

namespace Tests\Unit\App\Support\UserProfileTopicsOfInterestHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileTopicsOfInterest;
use App\Support\UserProfileTopicsOfInterestHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateOrCreateTopicsOfInterestTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $topicCode;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userId = 1;
        $this->clearingTopicCode = 'u9009';
        $this->dummyTopicCode = 'd1000';
    }

    /**
     * Check throws database exception if any write fails
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenInterestedUpdateFails(): void
    {
        $this->expectException(DatabaseException::class);

        $userProfileTopicsOfInterestMock = Mockery::mock('alias:' . UserProfileTopicsOfInterest::class)->makePartial();

        $userProfileTopicsOfInterestMock->user_id = $this->userId;
        $userProfileTopicsOfInterestMock->topic_code = $this->clearingTopicCode;

        $userProfileTopicsOfInterestMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(['user_id' => $this->userId, 'topic_code' => $this->clearingTopicCode,], ['topic_code' => $this->clearingTopicCode, 'user_id' => $this->userId, 'interested' => true])
            ->andThrow(new PDOException());

        UserProfileTopicsOfInterestHelper::updateOrCreateTopicsOfInterest(
            $this->userId,
            [$this->clearingTopicCode],
            [$this->dummyTopicCode]
        );
    }

    /**
     * Check throws database exception if any write fails
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenUninterestedUpdateFails(): void
    {
        $this->expectException(DatabaseException::class);

        $userProfileTopicsOfInterestMock = Mockery::mock('alias:' . UserProfileTopicsOfInterest::class)->makePartial();

        $userProfileTopicsOfInterestMock->user_id = $this->userId;
        $userProfileTopicsOfInterestMock->topic_code = $this->clearingTopicCode;

        $userProfileTopicsOfInterestMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(['user_id' => $this->userId, 'topic_code' => $this->clearingTopicCode,], ['topic_code' => $this->clearingTopicCode, 'user_id' => $this->userId, 'interested' => true])
            ->andReturnSelf();

        $userProfileTopicsOfInterestMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(['user_id' => $this->userId, 'topic_code' => $this->dummyTopicCode,], ['topic_code' => $this->dummyTopicCode, 'user_id' => $this->userId, 'interested' => false])
            ->andThrow(new PDOException());

        UserProfileTopicsOfInterestHelper::updateOrCreateTopicsOfInterest(
            $this->userId,
            [$this->clearingTopicCode],
            [$this->dummyTopicCode]
        );
    }

    /**
     * Check updateOrCreate is called on the model and delete is called for the remaining items
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateOrCreateIsCalled(): void
    {
        $userProfileTopicsOfInterestMock = Mockery::mock('alias:' . UserProfileTopicsOfInterest::class)->makePartial();

        $userProfileTopicsOfInterestMock->user_id = $this->userId;
        $userProfileTopicsOfInterestMock->topic_code = $this->clearingTopicCode;

        // Interested update
        $userProfileTopicsOfInterestMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'topic_code' => $this->clearingTopicCode,
                ],
                [
                    'topic_code' => $this->clearingTopicCode,
                    'user_id' => $this->userId,
                    'interested' => true
                ]
            )
            ->andReturnSelf();

        // Uninterested update
        $userProfileTopicsOfInterestMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'topic_code' => $this->dummyTopicCode,
                ],
                [
                    'topic_code' => $this->dummyTopicCode,
                    'user_id' => $this->userId,
                    'interested' => false
                ]
            )
            ->andReturnSelf();

        $userProfileTopicsOfInterestMock->shouldReceive('where')->once()->andReturnSelf();
        $userProfileTopicsOfInterestMock->shouldReceive('whereNotIn')->once()->andReturnSelf();
        $userProfileTopicsOfInterestMock->shouldReceive('delete')->andReturnNull();

        UserProfileTopicsOfInterestHelper::updateOrCreateTopicsOfInterest(
            $this->userId,
            [$this->clearingTopicCode],
            [$this->dummyTopicCode]
        );
    }
}
