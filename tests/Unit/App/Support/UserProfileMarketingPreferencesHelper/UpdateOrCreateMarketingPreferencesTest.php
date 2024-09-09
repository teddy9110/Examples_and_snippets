<?php

namespace Tests\Unit\App\Support\UserProfileMarketingPreferencesHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserMarketingPreference;
use App\Models\MarketingPreference;
use App\Support\UserProfileMarketingPreferencesHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;
use Carbon\Carbon;

class UpdateOrCreateMarketingPreferencesTest extends TestCase
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $marketingDataId;

    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->marketingDataId = 1;
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
    public function testThrowsExceptionWhenUpdateOrCreateFails(): void
    {
        $this->expectException(DatabaseException::class);

        $marketingPreferenceMock =  Mockery::mock('alias:' . MarketingPreference::class)->makePartial();
        $marketingPreferenceMock->id = 1;
        $marketingPreferenceMock->code = 'B2B2C_PARTNERS';
        $marketingPreferenceMock->name = 'Clearing';
        $marketingPreferenceMock->default_marketing_frequency = 'WEEKLY';

        $userProfileMarketingPreferenceMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();

        $marketingPreferenceMock->shouldReceive('where')->once()->andReturnSelf();

        $marketingPreferenceMock->shouldReceive('firstOrFail')->once()->andReturnSelf();

        $userProfileMarketingPreferenceMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1
                ],
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1,
                    'frequency' => 'WEEKLY',
                ]
            )->andThrow(new PDOException());

        UserProfileMarketingPreferencesHelper::updateOrCreateUserProfileMarketingPreference($this->userId, array('key' => 'B2B2C_PARTNERS', 'value' => 'Weekly'));
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
        $marketingPreferenceMock =  Mockery::mock('alias:' . MarketingPreference::class)->makePartial();
        $marketingPreferenceMock->id = 1;
        $marketingPreferenceMock->code = 'B2B2C_PARTNERS';
        $marketingPreferenceMock->name = 'Clearing';
        $marketingPreferenceMock->default_marketing_frequency = 'WEEKLY';

        $userProfileMarketingPreferenceMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();

        $marketingPreferenceMock->shouldReceive('where')->once()->andReturnSelf();

        $marketingPreferenceMock->shouldReceive('firstOrFail')->once()->andReturnSelf();

        $userProfileMarketingPreferenceMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1
                ],
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1,
                    'frequency' => 'WEEKLY',
                ]
            )->andReturn($userProfileMarketingPreferenceMock);

            UserProfileMarketingPreferencesHelper::updateOrCreateUserProfileMarketingPreference($this->userId, array('key' => 'B2B2C_PARTNERS', 'value' => 'Weekly'));
    }

    /**
     * Check updateOrCreate is called if isLegacy is true and the preference was updated before 20th June 2024
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateHappensIfNewValueDoesntExist()
    {
        $marketingPreferenceMock =  Mockery::mock('alias:' . MarketingPreference::class)->makePartial();
        $marketingPreferenceMock->shouldReceive('where')->once()->andReturnSelf();
        $marketingPreferenceMock->shouldReceive('firstOrFail')->once()->andReturnSelf();
        $marketingPreferenceMock->id = 1;
        $marketingPreferenceMock->code = 'B2B2C_PARTNERS';
        $marketingPreferenceMock->name = 'Clearing';
        $marketingPreferenceMock->default_marketing_frequency = 'WEEKLY';

        $builderMock = Mockery::mock(Builder::class);
        $userProfileMarketingPreferenceMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();
        $userProfileMarketingPreferenceMock->shouldReceive('where')->once()->with('user_id', $this->userId)->andReturn($builderMock);
        $builderMock->shouldReceive('where')->once()->with('marketing_preference_id', 1)->andReturn($builderMock);
        $builderMock->shouldReceive('where')->once()->with('updated_at', '>', '2024-06-20 00:00:00')->andReturn($builderMock);
        $builderMock->shouldReceive('get')->once()->andReturn(collect([]));

        $userProfileMarketingPreferenceMock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1
                ],
                [
                    'user_id' => $this->userId,
                    'marketing_preference_id' => 1,
                    'frequency' => 'WEEKLY',
                ]
            )->andReturn($userProfileMarketingPreferenceMock);
        UserProfileMarketingPreferencesHelper::updateOrCreateUserProfileMarketingPreference($this->userId, array('key' => 'B2B2C_PARTNERS', 'value' => 'Weekly'), true);
    }


    /**
     * Check no update happens if isLegacy is true and the preference was updated after 20th June 2024
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateDoesntHappenIfNewValueAlreadyExists()
    {
        $marketingPreferenceMock =  Mockery::mock('alias:' . MarketingPreference::class)->makePartial();
        $marketingPreferenceMock->shouldReceive('where')->once()->andReturnSelf();
        $marketingPreferenceMock->shouldReceive('firstOrFail')->once()->andReturnSelf();
        $marketingPreferenceMock->id = 1;
        $marketingPreferenceMock->code = 'B2B2C_PARTNERS';
        $marketingPreferenceMock->name = 'Clearing';
        $marketingPreferenceMock->default_marketing_frequency = 'WEEKLY';



        $builderMock = Mockery::mock(Builder::class);
        $userProfileMarketingPreferenceMock = Mockery::mock('alias:' . UserMarketingPreference::class)->makePartial();
        $userProfileMarketingPreferenceMock->shouldReceive('where')->once()->with('user_id', $this->userId)->andReturn($builderMock);
        $builderMock->shouldReceive('where')->once()->with('marketing_preference_id', 1)->andReturn($builderMock);
        $builderMock->shouldReceive('where')->once()->with('updated_at', '>', '2024-06-20 00:00:00')->andReturn($builderMock);
        $builderMock->shouldReceive('get')->once()->andReturn(collect([
            [
                'user_id' => $this->userId,
                'marketing_preference_id' => 1,
                'frequency' => 'WEEKLY',
                'updated_at' => Carbon::create(2024, 6, 21, 0, 0, 0)
            ]
        ]));

        $userProfileMarketingPreferenceMock->shouldNotReceive('updateOrCreate');
        UserProfileMarketingPreferencesHelper::updateOrCreateUserProfileMarketingPreference($this->userId, array('key' => 'B2B2C_PARTNERS', 'value' => 'Weekly'), true);
    }
}
