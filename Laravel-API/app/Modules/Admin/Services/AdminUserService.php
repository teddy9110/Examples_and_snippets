<?php

namespace Rhf\Modules\Admin\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Rhf\Modules\Activity\Models\AchievementWeek;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\MyFitnessPal\Services\DiaryService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Services\TargetService;
use Rhf\Modules\User\Services\UserService;
use Symfony\Component\HttpFoundation\ParameterBag;

class AdminUserService
{
    protected $targetService;
    protected $userService;

    /**
     * Create a new UserService instance.
     *
     * @param TargetService $targetService
     * @param UserService $userService
     */
    public function __construct(TargetService $targetService, UserService $userService)
    {
        $this->targetService = $targetService;
        $this->userService = $userService;
    }

    /**
     * Return the user associated to the instance of the service.
     *
     * @return User|null
     */
    public function getUser()
    {
        return isset($this->user) ? $this->user : null;
    }

    /**
     * Set the user associated to the instance of the service.
     *
     * @param User $user
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->targetService->setUser($user);
        $this->userService->setUser($user);
        return $this;
    }

    /**
     * Update the user.
     *
     * @param ParameterBag $data
     * @return User
     * @throws Exception
     */
    public function update(ParameterBag $data)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('No user set.');
        }

        $user = $this->getUser();

        $preferencesData = array_merge(
            $data->get('preferences', []),
            $data->get('profile', []),
            $data->get('goals', [])
        );

        if ($data->has('email')) {
            $user->email = $data->get('email');
        }

        if ($data->has('expiry_date')) {
            $user->expiry_date = Carbon::parse($data->get('expiry_date'));
        }

        if ($data->has('role_id')) {
            $user->role_id = $data->get('role_id');
        }

        if ($data->has('password')) {
            $user->password = Hash::make($data->get('password'));
        } elseif (!isset($user->password)) {
            $user->password = Hash::make(Str::random(10));
        }

        if ($data->has('paid')) {
            $user->paid = $data->get('paid', false);
        }

        if ($data->has('active')) {
            $user->active = $data->get('active', false);
        }

        if ($data->has('staff_user')) {
            $user->staff_user = $data->get('staff_user', false);
        }

        if (isset($preferencesData['first_name'])) {
            $user->first_name = $preferencesData['first_name'];
        }

        if (isset($preferencesData['last_name'])) {
            $user->surname = $preferencesData['last_name'];
        }

        try {
            $user->save();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new Exception("Failed to save user.");
        }

        if (!$user->preferences()->exists()) {
            $user->preferences()->create();
            $user->workoutPreferences()->create();
        }

        foreach ($preferencesData as $key => $value) {
            if (in_array($key, $user->preferences->getFillable())) {
                $user->setPreference($key, $value);
            }
        }

        $user->preferences->save();
        $user->workoutPreferences->save();

        // If admin panel, DO NOT recalculate goals
        if (!config('app.admin_panel')) {
            // Now need to check if targets are updated due to this
            $this->targetService->setUser($user);
            $this->userService->setUser($user);

            // Can only do targets if we have required data
            if ($this->targetService->canCalculateGoals($preferencesData)) {
                $this->userService->updateGoals($preferencesData);
                $user->activityLog()->create([
                    'action' => 'UserUpdateDetails'
                ]);
            }
        }

        return $user;
    }

    /**
     * Create the user.
     *
     * @param ParameterBag $data
     * @return User
     * @throws Exception
     */
    public function create(ParameterBag $data)
    {
        $user = new User();
        $this->setUser($user);
        $this->update($data);
        return $user;
    }

    /**
     * Calculate the achievements for the given number of weeks
     *
     * @param int $weeks
     * @return array
     */
    public function calculateWeeklyAchievement($weeks = 3)
    {
        $user = $this->getUser();
        $now = Carbon::now()->setTime(0, 0, 0);

        // sync activity data from MFP
        if ($user->hasConnectedMfp()) {
            $start = $now->copy()->subWeek($weeks);
            $end = $now->copy();
            DiaryService::sync($start, $end, $user);
        }

        $achievementWeeks = [];

        $activityService = (new ActivityService())->setUser($user);
        $activities = $activityService->from(now()->copy()->subWeeks($weeks))
            ->to(now()->copy())
            ->retrieve()
            ->get();

        // loop dates and create achievement week
        for ($i = $weeks; $i > 0; $i--) {
            $date = $now->copy()->subWeeks($i);

            $achievementWeek = new AchievementWeek(
                $date->copy()->startOfWeek(),
                $user,
                $activities->whereBetween('date', [$date->copy(), $date->copy()->addDays(6)]),
            );

            $achievementWeeks[] = [
                'from'  => $date->format('d/m/Y'),
                'to'  => $date->copy()->addWeek(1)->format('d/m/Y'),
                'medal' => $achievementWeek->getWeeklyMedal(),
                'stars' => $achievementWeek->getWeeklyStarsByMetric()
            ];
        }

        return $achievementWeeks;
    }
}
