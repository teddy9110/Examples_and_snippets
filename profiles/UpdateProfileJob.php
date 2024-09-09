<?php declare(strict_types=1);

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncGraphJob;
use App\Events\Subscribed\LeadCreated;
use App\Events\Subscribed\DPCProfileUpdated;
use App\Events\Subscribed\UserEmailUnsubscribed;
use App\Events\Publishes\ProfileUpdated;
use App\Support\UserProfileHelper;
use App\Support\UserProfileSubjectHelper;
use App\Support\UserProfileLearningProviderHelper;
use App\Support\UserProfileTopicsOfInterestHelper;

class UpdateProfileJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Profile Data
     *
     * @var LeadCreated|DPCProfileUpdated $event
     */
    public $event;

    /**
     * Create a new job instance.
     *
     * @param $request
     */
    public function __construct(LeadCreated|DPCProfileUpdated|UserEmailUnsubscribed $event) {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        try {
            $userId = $this->event->getUserID();
            if (!$userId) {
                return;
            }

            $profileData = $this->event->getProfileData();
            if ($profileData['university_start_year'] ?? null) {
                $profileData['intended_university_start_year'] = $profileData['university_start_year'];
                unset($profileData['university_start_year']);
            }

            $userProfile = null;
            // Main profile data
            if ($profileData) {
                $userProfile = UserProfileHelper::updateOrCreateUserProfile($userId, $profileData, $this->event->getQualificationIds());
            } else {
                $userProfile = UserProfileHelper::updateOrCreateUserProfile($userId, [], []);
            }

            if (!($this->event instanceof UserEmailUnsubscribed)) {

                // Subject Data
                if ($this->event->getCurrentSubjects()) {
                    UserProfileSubjectHelper::updateOrCreateSubjectsWithGrades($userId, $this->event->getCurrentSubjects(), 'current');
                }
                if ($this->event->getFutureSubjects()) {
                    UserProfileSubjectHelper::updateOrCreateSubjectsWithGrades($userId, $this->event->getFutureSubjects(), 'future');
                }
                if ($this->event->getPreviousSubjects()) {
                    UserProfileSubjectHelper::updateOrCreateSubjectsWithGrades($userId, $this->event->getPreviousSubjects(), 'previous');
                }

                // Learning Provider Data
                if ($this->event->getCurrentLearningProviders()) {
                    UserProfileLearningProviderHelper::updateOrCreateLearningProviders($userId, $this->event->getCurrentLearningProviders(), 'current');
                }
                if ($this->event->getFutureLearningProviders()) {
                    UserProfileLearningProviderHelper::updateOrCreateLearningProviders($userId, $this->event->getFutureLearningProviders(), 'future');
                }
                if ($this->event->getPreviousLearningProviders()) {
                    UserProfileLearningProviderHelper::updateOrCreateLearningProviders($userId, $this->event->getPreviousLearningProviders(), 'previous');
                }

                // Topics of Interest
                $clearingOptIn = $this->event->getClearingInterest();
                if (!is_null($clearingOptIn)) {
                    $clearingTopicCode = UserProfileTopicsOfInterestHelper::getClearingTopicCode();
                    $currentTopicsOfInterest = $userProfile->topicsOfInterest->map(function ($topic) {
                        return $topic->topic_code;
                    })->toArray();

                    if ($clearingOptIn) {
                        // Add the topic code for clearing
                        $currentTopicsOfInterest[] = $clearingTopicCode;
                    } elseif (in_array($clearingTopicCode, $currentTopicsOfInterest)) {
                        // Remove the topic code for clearing
                        $currentTopicsOfInterest = array_slice($currentTopicsOfInterest, array_search($clearingTopicCode, $currentTopicsOfInterest), 1);
                    }
                    UserProfileTopicsOfInterestHelper::updateOrCreateTopicsOfInterest($userId, array_unique($currentTopicsOfInterest));
                }
            }

            // Refresh the user profile model now it's been updated
            $userProfile->refresh();

            // Background job for syncing data into our graph
            SyncGraphJob::dispatch($userProfile);

            // Publish the profile updated event
            ProfileUpdated::publish($userProfile);

        } catch (Throwable $e) {
            Log::error($e);
            throw($e);
        }
    }
}
