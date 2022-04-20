<?php

namespace Rhf\Modules\User\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\User\Models\AppReviewTopic;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserAppStoreReviewFeedback;
use Rhf\Modules\User\Models\UserAppStoreReview;
use Rhf\Modules\User\Requests\UserAppStoreReviewRequest;
use Rhf\Modules\User\Requests\UserAppStoreFeedbackRequest;
use Rhf\Modules\User\Resources\UserAppStoreFeedbackResource;
use Rhf\Modules\User\Resources\UserAppStoreFeedbackTopicsResource;
use Sentry\Laravel\Facade as Sentry;
use Rhf\Modules\User\Resources\UserAppStoreReviewResource;
use Rhf\Modules\User\Services\UserAppStoreReviewService;
use Rhf\Modules\Zendesk\Services\ZendeskService;
use Throwable;

class UserAppStoreReviewController extends Controller
{
    public function userLeftReview(UserAppStoreReviewRequest $request)
    {
        $userAppReview = $this->updateUserAppReview($request);
        return new UserAppStoreReviewResource($userAppReview);
    }

    public function userFeedbackSubmitted(UserAppStoreFeedbackRequest $request)
    {
        $user = Auth::user();
        $userAppStoreReviewService = new UserAppStoreReviewService($user);
        $userReview = $userAppStoreReviewService->getUserAppStoreReview();
        $feedback = $this->storeFeedback($request, $userReview);

        $body = $userAppStoreReviewService->transformFeedbackForZendesk($request->all());

        $zendeskService = new ZendeskService();
        try {
            $zendeskService->createNewTicket(
                'App Feedback-API',
                'feedback@teamrhfitness.com',
                'App Feedback',
                $body,
                ['App Feedback']
            );
        } catch (Throwable $e) {
            Sentry::captureException('Error sending app store review feedback to Zendesk. '
                . $e->getCode() . ':' . $e->getMessage());
        }
        return new UserAppStoreFeedbackResource($feedback);
    }

    public function checkUserEligibility()
    {
        $user = Auth::user();

        $userAppStoreReviewService = new UserAppStoreReviewService($user);
        $userReview = $userAppStoreReviewService->getUserAppStoreReview();
        if (
            $userReview->user_response != null &&
            $userReview->user_response != 'dismiss' &&
            $userReview->present_review_dialog === false
        ) {
            return new UserAppStoreReviewResource($userReview);
        }
        $needsReviewBasedOnDays = $userReview->next_review_request->lte(Carbon::now()->startOfDay());

        if ($needsReviewBasedOnDays && $userAppStoreReviewService->hasUserLostWeight()) {
            $userReview->present_review_dialog = true;
            $userReview->save();
        }
        return new UserAppStoreReviewResource($userReview);
    }

    public function getFeedbackTopics()
    {
        $topics = $this->getTopicsFromCache();
        if (is_null($topics)) {
            $topics = AppReviewTopic::all();
            $this->cacheFeedbackTopics($topics);
            return UserAppStoreFeedbackTopicsResource::collection($topics);
        }
        return UserAppStoreFeedbackTopicsResource::collection(collect(json_decode($topics)));
    }

    private function cacheFeedbackTopics($topics)
    {
        Redis::set('user_app_store_review_topics', json_encode($topics), 'EX', 432000);
    }

    private function getTopicsFromCache()
    {
        return Redis::get('user_app_store_review_topics');
    }

    /**
     * @param UserAppStoreFeedbackRequest $request
     * @param $userReview
     * @return mixed
     */
    private function storeFeedback(UserAppStoreFeedbackRequest $request, $userReview)
    {
        $feedback = UserAppStoreReviewFeedback::create([
            'score' => $request->json('score'),
            'comments' => $request->json('comments', ''),
            'review_id' => $userReview->id,
        ]);
        $feedbackIds = AppReviewTopic::whereIn('slug', $request->json('feedback_topics'))->pluck('id');
        $feedback->topics()->syncWithoutDetaching($feedbackIds);
        return $feedback;
    }

    /**
     * @param UserAppStoreReviewRequest $request
     * @return mixed
     */
    private function updateUserAppReview(UserAppStoreReviewRequest $request)
    {
        $user = Auth::user();

        $userAppStoreReviewService = new UserAppStoreReviewService($user);
        $userAppReview = $userAppStoreReviewService->getUserAppStoreReview();

        $daysUntilNextReview = config('user.user_app_store_review_request_days');

        $lastReviewedDate = isset($userAppReview->last_review_submitted) ?
            $userAppReview->last_review_submitted :
            Carbon::now()->startOfDay();

        switch ($request->json('user_response')) {
            case 'yes':
                $userAppReview->next_review_request = null;
                $userAppReview->present_review_dialog = false;
                break;
            case 'no':
                $userAppReview->present_review_dialog = false;
                break;
            case 'dismiss':
                $userAppReview->present_review_dialog = false;
                $userAppReview->next_review_request = $lastReviewedDate->addDays($daysUntilNextReview)->startOfDay();
                break;
        }
        $userAppReview->last_review_submitted = Carbon::now()->startOfDay();
        $userAppReview->user_response = $request->json('user_response');
        $userAppReview->save();

        return $userAppReview;
    }
}
