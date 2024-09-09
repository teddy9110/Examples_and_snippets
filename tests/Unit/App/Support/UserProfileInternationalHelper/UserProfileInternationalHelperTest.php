<?php

namespace Tests\Unit\App\Support\UserProfileInternationalHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileInternationalApplication;
use App\Support\UserProfileInternationalHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class UserProfileInternationalHelperTest extends TestCase
{
    /**
     * Set up base test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userProfileInternationalHelper = new UserProfileInternationalHelper();
    }

    /**
     * Check that if a user exists 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateOrCreateInternationalUserDataSuccess()
    {
        $userId = 2; // Replace with a valid user ID
        $data = [
            'international_questions' => [
                'user_id' => $userId,
                'country_of_nationality' => 'Country',
                'is_application_started' => true,
                'international_agent' => 'test',
            ]];

        $userProfileInternationalApplicationMock = Mockery::mock('alias:' . UserProfileInternationalApplication::class)->makePartial();
        $userProfileInternationalApplicationMock->shouldReceive('where')->andReturnSelf();
        $userProfileInternationalApplicationMock->shouldReceive('first')->andReturnSelf();
        $userProfileInternationalApplicationMock->shouldReceive('update')
            ->with($data['international_questions'])
            ->once()
            ->andReturn($userProfileInternationalApplicationMock);
        $userProfileInternationalApplicationMock->shouldReceive('save')
            ->once()
            ->andReturn($userProfileInternationalApplicationMock);

        $this->assertNull($this->userProfileInternationalHelper->updateOrCreateInternationalUserData($data, $userId));
    }

    /**
     * Check that function creates the user data if none is found 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateOrCreateInternationalUserDataSuccessWhenNoUserDataFound()
    {
        $userId = 2; // Replace with a valid user ID
        $data = [
            'international_questions' => [
                'user_id' => $userId,
                'country_of_nationality' => 'Country',
                'is_application_started' => true,
                'international_agent' => 'test',
            ]];

        $userProfileInternationalApplicationMock = Mockery::mock('alias:' . UserProfileInternationalApplication::class)->makePartial();
        $userProfileInternationalApplicationMock->shouldReceive('where')->andReturnSelf();
        $userProfileInternationalApplicationMock->shouldReceive('first')->andReturnNull();
        $userProfileInternationalApplicationMock->shouldReceive('create')->andReturnSelf();
        $userProfileInternationalApplicationMock->shouldReceive('update')
            ->with($data['international_questions'])
            ->once()
            ->andReturn($userProfileInternationalApplicationMock);
        $userProfileInternationalApplicationMock->shouldReceive('save')
            ->once()
            ->andReturn($userProfileInternationalApplicationMock);

        $this->assertNull($this->userProfileInternationalHelper->updateOrCreateInternationalUserData($data, $userId));
    }

    /**
     * Check throws database exception if any write fails
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @throws \ReflectionException
     */
    public function testUpdateOrCreateInternationalUserDataException()
    {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Error updating or creating the user\'s international data');

        $data = [
            'international_questions' => [
                'country_of_nationality' => 'Country',
                'is_application_started' => true,
                'international_agent' => 'test'
            ]];

        $userId = 1;

        $userProfileInternationalApplicationMock = Mockery::mock('alias:' . UserProfileInternationalApplication::class)->makePartial();

        $userProfileInternationalApplicationMock->shouldReceive('updateOrCreate')
            ->with(['user_id' => $userId], $data['international_questions'])
            ->andThrow(new \Exception('Database error'));

            $this->userProfileInternationalHelper->updateOrCreateInternationalUserData($data, $userId);
    }
}
