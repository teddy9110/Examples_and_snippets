<?php

namespace Tests\Unit\App\Support\UserProfileQualificationsHelper;

use App\Exceptions\DatabaseException;
use App\Models\UserProfileQualification;
use App\Support\UserProfileQualificationsHelper;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class ReplaceUsersQualificationsForStageTest extends TestCase
{
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

        $qualifications = [
            [
                'qualification_id' => 2,
                'start_year' => null,
                'end_year' => 2025
            ]
        ];

        $userProfileQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $userProfileQualificationMock->shouldReceive('updateOrCreate')->once()->andThrow(new PDOException());

        UserProfileQualificationsHelper::replaceUsersQualificationsForStage(1, 'current', $qualifications);
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
        $qualifications = [
            [
                'qualification_id' => 2,
                'start_year' => null,
                'end_year' => 2025
            ]
        ];

        // Create a mock for UserProfileQualification
        $userProfileQualificationMock = Mockery::mock('alias:' . UserProfileQualification::class)->makePartial();
        $userProfileQualificationMock->shouldReceive('updateOrCreate')->once()
            ->with(
                [
                    'user_id' => 1,
                    'qualification_id' => 2,
                    'current' => true,
                    'previous' => false,
                    'future' => false,
                ],
                [
                    'user_id' => 1,
                    'qualification_id' => 2,
                    'start_year' => null,
                    'end_year' => 2025,
                    'current' => true,
                    'previous' => false,
                    'future' => false,
                ]
            )
            ->andReturn(null);
        

        $userProfileQualificationMock->shouldReceive('where')->once()->with('user_id', 1)->andReturnSelf();
        $userProfileQualificationMock->shouldReceive('whereNotIn')->once()->with('qualification_id', [2])->andReturnSelf();
        $userProfileQualificationMock->shouldReceive('where')->once()->with('current', true)->andReturnSelf();
        $userProfileQualificationMock->shouldReceive('count')->once()->andReturn(1);
        $userProfileQualificationMock->shouldReceive('delete')->once()->andReturn(null);

        UserProfileQualificationsHelper::replaceUsersQualificationsForStage(1, 'current', $qualifications);
    }
    
}
