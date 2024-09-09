<?php

namespace Tests\Unit\App\Support\UserProfileLearningProviderHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileLearningProvider;
use App\Support\UserProfileLearningProviderHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UpdateOrCreateLearningProviderTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $learningProviderId;

    /**
     * @var string
     */
    protected $learningProviderStage;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userId = 1;
        $this->learningProviderId = 2;
        $this->learningProviderStage = 'future';
    }

    /**
     * Check throws database exception if any write fails
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testThrowsExceptionWhenUpdateFails(): void
    {
        $this->expectException(DatabaseException::class);

        $userProfileLearningProviderMock = Mockery::mock('alias:' . UserProfileLearningProvider::class)->makePartial();

        $userProfileLearningProviderMock->user_id = $this->userId;
        $userProfileLearningProviderMock->learning_provider_id = $this->learningProviderId;

        $userProfileLearningProviderMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(['user_id' => $this->userId, 'learning_provider_id' => $this->learningProviderId,], ['learning_provider_id' => $this->learningProviderId, 'user_id' => $this->userId, 'current' => false, 'previous' => false, 'future' => true,])
            ->andThrow(new PDOException());

        UserProfileLearningProviderHelper::updateOrCreateLearningProviders(
            $this->userId,
            [$this->learningProviderId],
            $this->learningProviderStage
        );
    }

    /**
     * Check update is called on the model if the learning provider exists
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateIsCalledIfLearningProviderExists(): void
    {
        $userProfileLearningProviderMock = Mockery::mock('alias:' . UserProfileLearningProvider::class)->makePartial();

        $userProfileLearningProviderMock->user_id = $this->userId;
        $userProfileLearningProviderMock->learning_provider_id = $this->learningProviderId;

        $userProfileLearningProviderMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'learning_provider_id' => $this->learningProviderId,
                ],
                [
                    'learning_provider_id' => $this->learningProviderId,
                    'user_id' => $this->userId,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ]
            )
            ->andReturnSelf();

        $userProfileLearningProviderMock->shouldReceive('where')
            ->twice()->andReturnSelf();

        $userProfileLearningProviderMock->shouldReceive('whereNotIn')
            ->once()->andReturnSelf();

        $userProfileLearningProviderMock->shouldReceive('get')->andReturn(new Collection());

        $userProfileLearningProviderMock->shouldReceive('delete')->andReturnNull();

        UserProfileLearningProviderHelper::updateOrCreateLearningProviders(
            $this->userId,
            [$this->learningProviderId],
            $this->learningProviderStage
        );
    }

    /**
     * Check create is called if the learning provider does not exist
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testCreateIsCalledIfLearningProviderDoesNotExist()
    {
        $userProfileLearningProviderMock = Mockery::mock('alias:' . UserProfileLearningProvider::class)->makePartial();
        $userProfileLearningProviderMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'learning_provider_id' => $this->learningProviderId,
                ],
                [
                    'learning_provider_id' => $this->learningProviderId,
                    'user_id' => $this->userId,
                    'current' => false,
                    'previous' => false,
                    'future' => true
                ]
            )
            ->andReturn($userProfileLearningProviderMock);

        $userProfileLearningProviderMock->shouldReceive('where')
            ->twice()->andReturn($userProfileLearningProviderMock);

        $userProfileLearningProviderMock->shouldReceive('whereNotIn')
            ->once()->andReturn($userProfileLearningProviderMock);

        $userProfileLearningProviderMock->shouldReceive('get')
            ->once()->andReturn($userProfileLearningProviderMock);

        $userProfileLearningProviderMock->shouldNotReceive('delete');

        UserProfileLearningProviderHelper::updateOrCreateLearningProviders(
            $this->userId,
            [$this->learningProviderId],
            $this->learningProviderStage
        );
    }
}
