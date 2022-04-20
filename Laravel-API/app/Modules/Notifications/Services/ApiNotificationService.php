<?php

namespace Rhf\Modules\Notifications\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Rhf\Modules\Notifications\Actions\NotificationActions;
use Rhf\Modules\Notifications\Models\ApiNotification;
use Rhf\Modules\Notifications\Models\UserApiNotification;
use Rhf\Modules\System\Models\Feature;

class ApiNotificationService
{
    public function createNotification($data)
    {
        return ApiNotification::create($data);
    }

    /**
     * Get all notifications
     *
     * @return mixed
     */
    public function getNotifications(array $params)
    {
        $query = $this->buildQuery($params);
        $results = $query->get();
        $filteredResults = $this->filterByFeatureFlag($results);
        $filteredResults = $this->filterDismissedNotifications($filteredResults);
        return $this->isDirectDebitSubscriber($filteredResults);
    }

    /**
     * get the notification
     *
     * @param $id
     * @return mixed
     */
    public function getNotification($id)
    {
        return ApiNotification::findOrFail($id);
    }

    /**
     * Acknowledge notification and carry out any appropriate action
     *
     * @param $id
     * @return mixed
     */
    public function handleNotification($id)
    {
        $notificationHandled = UserApiNotification::where('user_id', auth('api')->user()->id)
            ->where('notification_id', $id)
            ->first();
        if (isset($notificationHandled)) {
            return;
        }

        $notification = $this->getNotification($id);
        $callback = Str::camel($notification->action_callback);
        if ($this->hasAction($callback)) {
            $this->completeAction($callback);
        }
        return $this->dismissNotification($notification->id);
    }

    /**
     * Build a search query based off parameters.
     *
     * @param $params
     * @return Builder
     */
    private function buildQuery(array $params = []): Builder
    {
        $query = ApiNotification::where(function ($q) use ($params) {
            $q->where('not_before', '<', now()->format('Y-m-d H:i:s'))->orWhereNull('not_before');
        })->where(function ($q) use ($params) {
            $q->where('not_after', '>', now()->format('Y-m-d H:i:s'))->orWhereNull('not_after');
        });

        if (isset($params['platform'])) {
            $query->where(function ($q) use ($params) {
                $q->where('platform', strtolower($params['platform']))->orWhere('platform', 'all');
            });
        }

        if (isset($params['app_version'])) {
            $query->where(function ($q) use ($params) {
                $removeUnderscore = explode('_', $params['app_version']);
                $appVersion = str_replace('.', '', $removeUnderscore[0]);
                $q->whereRaw("cast(replace(app_version, '.', '') as unsigned integer) <= {$appVersion}")
                    ->orWhereNull('app_version');
            });
        }

        if (isset($params['platform_version'])) {
            $query->where('platform_version', '<=', $params['platform_version']);
        }

        return $query;
    }

    /**
     * Add record showing notification has been read
     *
     * @return mixed
     */
    private function dismissNotification($id)
    {
        return UserApiNotification::create([
            'notification_id' => $id,
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Check if notification has corresponding action
     *
     * @param $title
     * @return bool
     */
    private function hasAction($title): bool
    {
        $action = new NotificationActions();
        return method_exists($action, $title);
    }

    /**
     * Complete action
     *
     * @param $callback
     */
    private function completeAction($callback): void
    {
        $action = new NotificationActions();
        call_user_func(array($action, $callback));
    }

    /**
     * Filter out notifications where a feature_flag requirement is present and
     * the feature is not active.
     *
     * @param Collection $results
     * @return Collection
     */
    private function filterByFeatureFlag(Collection $results): Collection
    {
        $featureFlags = Feature::all()->keyBy(fn ($item) => strtolower($item->slug));

        return $results->filter(function ($item) use ($featureFlags) {
            if (is_null($item->feature_flag)) {
                return true;
            }
            $feature = $featureFlags->get(strtolower($item->feature_flag));
            return is_null($feature) ? true : $feature->active == 1;
        });
    }

    /**
     * Filter out notifications that the user has already dismissed.
     *
     * @param Collection $results
     * @return Collection
     */
    private function filterDismissedNotifications(Collection $results): Collection
    {
        $hasDismissed = UserApiNotification::where('user_id', Auth::id())->get();

        if (!$hasDismissed->isEmpty()) {
            $rejected = $results->reject(function ($value) use ($hasDismissed) {
                foreach ($hasDismissed as $dismissed) {
                    if ($value['id'] === $dismissed->notification_id) {
                        return true;
                    }
                }
            });
            return collect($rejected->all());
        }
        return $results;
    }

    /**
     * Check if user is a direct debit subscriber
     * Check if Notification contains Direct Debit in title
     * Remove notification that contains direct debit
     *
     * @param Collection $results
     * @return Collection
     */
    private function isDirectDebitSubscriber(Collection $results): Collection
    {
        $user = auth()->user();
        $isDirectDebit = $user->subscription && $user->subscription->subscription_provider == 'directdebit';

        if (!config('app.ab_to_sd_notifications_enabled') || !$isDirectDebit) {
            $rejected = $results->reject(function ($value) {
                return strtolower($value['action_callback']) == 'ashbourne-to-smart-debit';
            });
            return collect($rejected->all());
        }
        return $results;
    }
}
