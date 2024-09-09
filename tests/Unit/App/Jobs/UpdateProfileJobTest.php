<?php

namespace Tests\Unit\App\Jobs;

use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Feedback;
use Mockery;
use Tests\TestCase;
use App\Jobs\UpdateProfileJob;
use App\Jobs\SyncGraphJob;
use App\Support\UserProfileHelper;
use App\Support\UserProfileSubjectHelper;
use App\Support\UserProfileLearningProviderHelper;
use App\Support\UserProfileTopicsOfInterestHelper;
use App\Events\Subscribed\DPCProfileUpdated;
use App\Events\Subscribed\LeadCreated;
use App\Events\Subscribed\UserEmailUnsubscribed;
use App\Models\UserProfile;
use TSR\EventBridgeIngestion\Publisher;

class UpdateProfileJobTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that the job processes the event, updates the profile and queues jobs to sync to other apps
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testHandleDPCProfileUpdatedEvent(): void
    {
        // Mock sending ProfileUpdated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        $graphMock = Mockery::mock(Graph::class)->makePartial();
        $graphMock->shouldReceive('getUserFirstPartyTopics')->once()->andReturn([]);

        app()->bind("Graph", function () use ($graphMock) {
            return $graphMock;
        });

        Queue::fake();

        $profileMock = Mockery::mock('alias:' . UserProfile::class);
        $profileMock->shouldReceive('refresh')->once()->andReturn(null);
        $profileMock->shouldReceive('withoutRelations')->andReturn($profileMock);
        $profileMock->shouldReceive('toArray')->andReturn([]);
        $profileMock->shouldReceive('getEmailMarketingPreferences')->andReturn([]);
        $profileMock->topicsOfInterest = collect([]);
        $profileMock->qualifications = collect([]);
        $profileMock->user_id = 1;

        $profileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class);
        $profileHelperMock->shouldReceive('updateOrCreateUserProfile')
            ->once()
            ->andReturn($profileMock);

        Mockery::mock('alias:' . UserProfileSubjectHelper::class)
            ->shouldReceive('updateOrCreateSubjectsWithGrades')
            ->times(3)
            ->andReturn(null);

        Mockery::mock('alias:' . UserProfileLearningProviderHelper::class)
            ->shouldReceive('updateOrCreateLearningProviders')
            ->times(3)
            ->andReturn(null);

        $topicHelperMock = Mockery::mock('alias:' . UserProfileTopicsOfInterestHelper::class);
        $topicHelperMock->shouldReceive('updateOrCreateTopicsOfInterest')
            ->once()
            ->andReturn(null);
        $topicHelperMock->shouldReceive('getClearingTopicCode')
            ->once()
            ->andReturn('u9009');

        $event = [
            'detail' => [
                'user_id' => 1,
                'clearing_optin' => 1,
                'university_start_year' => 2021,
                'qualifications' => [1],
                'current_subjects' => [2],
                'future_subjects' => [3],
                'previous_subjects' => [4],
                'current_learning_providers' => [5],
                'future_learning_providers' => [6],
                'previous_learning_providers' => [7],
            ]
        ];

        $job = new UpdateProfileJob(new DPCProfileUpdated($event));
        $job->handle();

        Queue::assertPushed(SyncGraphJob::class);
    }

    /**
     * Test that the job processes the event, updates the profile and queues jobs to sync to other apps
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testHandleLeadCreatedEvent(): void
    {
        // Mock sending ProfileUpdated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        $graphMock = Mockery::mock(Graph::class)->makePartial();
        $graphMock->shouldReceive('getUserFirstPartyTopics')->once()->andReturn([]);

        app()->bind("Graph", function () use ($graphMock) {
            return $graphMock;
        });

        Queue::fake();

        $profileMock = Mockery::mock('alias:' . UserProfile::class);
        $profileMock->shouldReceive('refresh')->once()->andReturn(null);
        $profileMock->shouldReceive('withoutRelations')->andReturn($profileMock);
        $profileMock->shouldReceive('toArray')->andReturn([]);
        $profileMock->shouldReceive('getEmailMarketingPreferences')->andReturn([]);
        $profileMock->topicsOfInterest = collect([]);
        $profileMock->qualifications = collect([]);
        $profileMock->user_id = 1;

        $profileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class);
        $profileHelperMock->shouldReceive('updateOrCreateUserProfile')
            ->once()
            ->andReturn($profileMock);

        Mockery::mock('alias:' . UserProfileSubjectHelper::class)
            ->shouldNotReceive('updateOrCreateSubjectsWithGrades');

        Mockery::mock('alias:' . UserProfileLearningProviderHelper::class)
            ->shouldNotReceive('updateOrCreateLearningProviders');


        $event = [
            'detail' => [
                'lead_info' => [
                    'user_id' => 1,
                ],
                'user_info' => [
                    'university_start_year' => 2021,
                ]
            ]
        ];

        $job = new UpdateProfileJob(new LeadCreated($event));
        $job->handle();

        Queue::assertPushed(SyncGraphJob::class);
    }

    /**
     * Test that the job processes the event, updates the profile and queues jobs to sync to other apps
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testHandleUserEmailUnsubscribedEvent(): void
    {
        // Mock sending ProfileUpdated event to eventbridge
        $publisherMock = Mockery::mock('overload:'.Publisher::class);
        $publisherMock->shouldReceive('publish')->once();

        $graphMock = Mockery::mock(Graph::class)->makePartial();
        $graphMock->shouldReceive('getUserFirstPartyTopics')->once()->andReturn([]);

        app()->bind("Graph", function () use ($graphMock) {
            return $graphMock;
        });

        Queue::fake();

        $profileMock = Mockery::mock('alias:' . UserProfile::class);
        $profileMock->shouldReceive('refresh')->once()->andReturn(null);
        $profileMock->shouldReceive('withoutRelations')->andReturn($profileMock);
        $profileMock->shouldReceive('toArray')->andReturn([]);
        $profileMock->shouldReceive('getEmailMarketingPreferences')->andReturn([]);
        $profileMock->topicsOfInterest = collect([]);
        $profileMock->qualifications = collect([]);
        $profileMock->user_id = 1;

        $profileHelperMock = Mockery::mock('alias:' . UserProfileHelper::class);
        $profileHelperMock->shouldReceive('updateOrCreateUserProfile')
            ->once()
            ->andReturn($profileMock);

        Mockery::mock('alias:' . UserProfileSubjectHelper::class)
            ->shouldNotReceive('updateOrCreateSubjectsWithGrades');

        Mockery::mock('alias:' . UserProfileLearningProviderHelper::class)
            ->shouldNotReceive('updateOrCreateLearningProviders');


        $event = [
            'detail' => [
                'user_id' => 1,
                'unsubscribe_date' => '2021-01-01 00:00:00'
            ]
        ];

        $job = new UpdateProfileJob(new UserEmailUnsubscribed($event));
        $job->handle();

        Queue::assertPushed(SyncGraphJob::class);
    }
}
