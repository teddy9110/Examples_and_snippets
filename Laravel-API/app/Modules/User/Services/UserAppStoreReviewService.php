<?php

namespace Rhf\Modules\User\Services;

use Carbon\Carbon;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\User\Models\AppReviewTopic;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserAppStoreReview;

class UserAppStoreReviewService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function hasUserLostWeight(): bool
    {
        $startWeight = $this->getUser()->getPreference('start_weight');
        $currentWeight = Activity::where('user_id', $this->getUser()->id)
            ->where('type', 'weight')
            ->orderBy('date', 'DESC')
            ->first();

        if (is_null($currentWeight)) {
            return false;
        }

        return $startWeight > $currentWeight->value;
    }

    public function transformFeedbackForZendesk(array $values): string
    {
        $message = $this->user->name . '(' . $this->user->email . ') has submitted the following responses: ' . PHP_EOL;
        $message .= PHP_EOL;
        foreach ($values as $key => $value) {
            if ($key == 'feedback_topics') {
                $message .= 'Was raised as an issue:' . PHP_EOL;
                foreach ($value as $section) {
                    $message .= '- ' . AppReviewTopic::where('slug', $section)->first()->title  . PHP_EOL;
                }
            } else {
                $message .= $key . ' = ' . $value . PHP_EOL;
            }
        }
        return $message;
    }

    public function getUserAppStoreReview()
    {
        $review = $this->user->appStoreReview;
        if (!is_null($review)) {
            return $review;
        }

        $daysAccountExists = $this->user->created_at->startOfDay()->diffInDays(now()->startOfDay(), true);
        $daysTillReview = $daysAccountExists >= 10 ? 0 : 10 - $daysAccountExists;

        return UserAppStoreReview::create(
            [
                'user_id' => $this->user->id,
                'present_review_dialog' => false,
                'next_review_request' => Carbon::now()->addDays($daysTillReview)->startOfDay(),
            ]
        );
    }

    /**************************************************
     *
     * GETTERS
     *
     ***************************************************/

    /**
     * Return the user associated to the instance of the service.
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }


    /**************************************************
     *
     * SETTERS
     *
     ***************************************************/

    /**
     * Set the user associated to the instance of the service.
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
