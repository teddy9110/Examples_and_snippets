<?php

namespace Rhf\Modules\User\Services;

use Exception;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Modules\System\Exceptions\DisplayedErrorException;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\System\Models\ActivityLog;
use Rhf\Modules\User\Models\UserPreferences;
use Rhf\Modules\Workout\Services\UserWorkoutService;

class UserService
{
    protected TargetService $targetService;

    protected ?User $user;

    /**
     * Create a new UserService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->targetService = new TargetService();
    }

    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Check if the user has hit any update thresholds.
     *
     * @return bool
     * @throws Exception
     */
    public function canUpdate()
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('No user set.');
        }

        $count = $this->getUser()->weekActivityLog()->count();

        if ($count >= config('user.profile_updates')) {
            // Create the new log
            $log = new ActivityLog();
            $log->user_id = $this->getUser()->id;
            $log->action = 'UserUpdateDetails - Fail';
            $log->save();

            return false;
        }

        return true;
    }

    /**
     * Return filtered list of users.
     *
     * @return User
     */
    public static function filtered()
    {
        $userModel = new User();

        // Check for relevant filter conditions and apply to query object
        if (request()->get('search')['value'] != null) {
            $userModel = $userModel->search(request()->get('search'));
        }

        // Check for order by
        // TODO: Check we are setting the correct request key for "order"
        if (request()->has('order') && request()->get('order') != '') {
            $order = request()->get('columns')[request()->get('order')[0]['column']]['name'];
            $direction = request()->get('order')[0]['dir'];
            if ($order != '' && $direction != '') {
                $userModel = $userModel->orderBy($order, $direction);
            }
        }

        return $userModel;
    }

    /**
     * Update the user.
     *
     * @param array
     * @return self
     * @throws DisplayedErrorException
     */
    public function update($data)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('No user set.');
        }

        $user = $this->getUser();

        $originalPreferences =  clone $user->preferences;
        if ($user->preferences == null) {
            $user->preferences()->create();
        }
        if ($user->workoutPreferences == null) {
            $user->workoutPreferences()->create();
        }

        // Check the user is allowed to be updated
        if (!$this->canUpdate()) {
            throw new DisplayedErrorException(
                'Sorry, you are not allowed to update your details that frequently. Please try again later.'
            );
        }

        // TODO Temporary fix for old apps, remove at later date
        if (!isset($data['medical_conditions_consent'])) {
            $data['medical_conditions_consent'] = true;
        }

        // make sure user consents to medical conditions
        if (isset($data['medical_conditions'])) {
            $consent = isset($data['medical_conditions_consent'])
                ? $data['medical_conditions_consent']
                : $user->preferences->medical_conditions_consent;
            if (!$consent) {
                throw new FitnessHttpException("You cannot supply medical conditions without consenting to use.", 422);
            }
        }

        // Loop, assign and update
        foreach ($data as $key => $value) {
            // Check if is password and manually create
            if ($key == 'password') {
                $user->updatePassword($value);
            } elseif ($key == 'user_role') {
                $user->role_id = $value;
            } elseif ($key == 'gender' && $user->preferences->gender != null && $user->preferences->gender != $value) {
                throw new FitnessHttpException("Gender cannot be changed.", 422);
            } elseif ($key == 'medical_conditions_consent' && $value == false) {
                $user->preferences->medical_conditions_consent = false;
                $user->preferences->medical_conditions = null;
            } elseif ($key == 'progress_picture_consent') {
                $currentConsent = $user->preferences->progress_picture_consent;

                $validValues = ['accepted', 'rejected'];
                if ($currentConsent == 'unknown') {
                    $validValues[] = 'unknown';
                }
                if (!in_array($value, $validValues)) {
                    throw new FitnessHttpException("Progress picture consent has already been set", 422);
                }

                $user->preferences->progress_picture_consent = $value;
            } elseif (in_array($key, $user->preferences->getFillable()) || $key == 'token') { // is this a preference?
                $user->setPreference($key, $value);
            } elseif (in_array($key, $user->getFillable())) { // is this a user property?
                $user->$key = $value;
            } else { // must be a direct user property
                throw new Exception("Invalid property {$key}");
            }
        }

        // Check the users preferences to see if the workout preferences have changed.
        // If they have, reset workout schedule.
        if ($user->workoutPreferences->isDirty('exercise_frequency_id', 'exercise_location_id', 'exercise_level_id')) {
            $service = new UserWorkoutService($user);
            $service->saveWorkoutSchedule(null);
        }

        $user->save();
        $user->preferences->save();
        $user->workoutPreferences->save();
        // need to refresh the user in order to get workout preferences
        // for updateGoals and to reset the user with the updated preferences
        $user->refresh();
        $this->setUser($user);

        // Now need to check if targets are updated due to this
        $this->targetService->setUser($this->getUser());

        // Can only do targets if we have required data
        if ($this->targetService->canCalculateGoals($data)) {
            $this->updateGoals($data, $originalPreferences);

            // Create the new log
            $log = new ActivityLog();
            $log->user_id = $this->getUser()->id;
            $log->action = 'UserUpdateDetails';
            $log->save();
        }

        return $this;
    }

    /**
     * Update the user goals.
     *
     * $currentPreferences are passed in as a temporary fix for calorie goal calculation. DO NOT use
     * this parameter unless absolutely necessary.
     *
     * @param array $data
     * @param UserPreferences|null $currentPreferences
     * @return self
     * @throws Exception
     */
    public function updateGoals($data = [], UserPreferences $currentPreferences = null)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('No user set.');
        }

        // If preferences don't exist, we must create them
        if (!$this->user->preferences) {
            $this->user->preferences()->create();
            $this->user->refresh();
        }

        // Can only do targets if we have required data
        if ($this->targetService->canCalculateGoals($data)) {
            // Add the default water goal
            $defaultWaterGoal = 8;
            if (api_version() >= 20210914) {
                $defaultWaterGoal = $defaultWaterGoal * 200;
            }
            $this->updatePreference('daily_water_goal', $defaultWaterGoal);

            if (
                !is_null($currentPreferences) &&
                isset($data['daily_calorie_goal']) &&
                isset($data['daily_step_goal'])
            ) {
                /**
                 * This is a bit of a hacky solution, but needed to be done in a time sensitive manner.
                 * Until the time of this fix, apps sent only the preferences that needed to be changed, which means
                 * steps & calorie goals would not get sent in the same request.
                 *
                 * An iOS update caused all preferences to always be sent within the same request, even if not changed,
                 * which meant that if a customer would update their steps target, the calories would not get updated
                 * accordingly. To avoid that, we need to handle a scenario where both preferences are set.
                 * Currently, it would solve it in a following way:
                 * - If both preferences are different to current values - set both to provide values.
                 * - If only calories differ from current value - set to provided value.
                 * - Otherwise fall back to a calculated calorie goal solution.
                 */


                $currentCals = $currentPreferences->daily_calorie_goal;
                $currentSteps = $currentPreferences->daily_step_goal;

                if ($currentCals != $data['daily_calorie_goal'] && $currentSteps != $data['daily_step_goal']) {
                    // If both preferences are set and are different from current - set to provided values.
                    $this->updatePreference('daily_calorie_goal', $data['daily_calorie_goal']);
                    $this->updatePreference('daily_step_goal', $data['daily_step_goal']);
                } elseif ($currentCals != $data['daily_calorie_goal']) {
                    // If only daily calorie goal is different from current - overwrite the value.
                    $this->updatePreference('daily_calorie_goal', $data['daily_calorie_goal']);
                } else {
                    // Fallback to calculated value.
                    $this->updatePreference(
                        'daily_calorie_goal',
                        $this->targetService->calculateCalorieGoal(
                            $this->getUser()->getPreference('start_weight'),
                            $this->getUser()->getPreference('daily_step_goal'),
                            $this->getUser()->exerciseLocation,
                            $this->getUser()->exerciseFrequency
                        )
                    );
                }
            } else {
                // Only update calories if not passed in the request
                if (!isset($data['daily_calorie_goal'])) {
                    $this->updatePreference(
                        'daily_calorie_goal',
                        $this->targetService->calculateCalorieGoal(
                            $this->getUser()->getPreference('start_weight'),
                            $this->getUser()->getPreference('daily_step_goal'),
                            $this->getUser()->exerciseLocation,
                            $this->getUser()->exerciseFrequency
                        )
                    );
                } else {
                    $this->updatePreference('daily_calorie_goal', $data['daily_calorie_goal']);
                }
            }

            // TODO: Deprecate eventually
            if ((!grhaft_enabled() || !api_version()) && !config('app.admin_panel')) {
                $this->updatePreference(
                    'exercise_level_id',
                    $this->targetService->calculateExerciseLevel(
                        $this->getUser()->getPreference('start_weight'),
                        $this->getUser()->exerciseLocation,
                        $this->getUser()->exerciseFrequency
                    )->id
                );
            }

            if (!config('app.admin_panel')) {
                $this->updatePreference(
                    'daily_protein_goal',
                    $this->targetService->calculateNutritionGoal(
                        'protein',
                        $this->getUser()->getPreference('daily_calorie_goal')
                    )
                );
                $this->updatePreference(
                    'daily_carbohydrate_goal',
                    $this->targetService->calculateNutritionGoal(
                        'carbs',
                        $this->getUser()->getPreference('daily_calorie_goal')
                    )
                );
                $this->updatePreference(
                    'daily_fat_goal',
                    $this->targetService->calculateNutritionGoal(
                        'fat',
                        $this->getUser()->getPreference('daily_calorie_goal')
                    )
                );
                $this->updatePreference(
                    'daily_fiber_goal',
                    $this->targetService->calculateNutritionGoal(
                        'fiber',
                        $this->getUser()->getPreference('daily_calorie_goal')
                    )
                );
            }
        }
        $this->user->preferences->save();
        return $this;
    }

    /**
     * Update the user meta.
     *
     * @param string
     * @param string
     * @param int
     * @return self
     * @throws Exception
     */
    public function updatePreference($key, $value)
    {
        // Check we have a user
        if (!$this->getUser()) {
            throw new Exception('No user set.');
        }

        // Check the value we are setting
        if ($value && $value != null) {
            $this->user->setPreference($key, $value);
        }

        // If the user is trying to unset their medical conditions or personal goals
        if (is_null($value) && in_array($key, ['medical_conditions', 'personal_goals'])) {
            if ($this->user->hasPreference($key)) {
                $this->user->setPreference($key, $value);
            }
        }

        return $this;
    }

    /**
     * Updated specified user preferences.
     *
     * @param data
     * @param value
     * @return null
     * @throws Exception
     */
    public function updatePreferences($data)
    {
        foreach ($data as $key => $value) {
            $this->updatePreference($key, $value);
        }

        $this->user->preferences->save();
    }

    /**
     * Remove the array of keys passed in from the users meta if they exists
     * @param $keys
     * @throws Exception
     */
    public function removePreferences($keys)
    {
        // Check we have a user
        if (is_null($this->getUser())) {
            throw new Exception('No user set.');
        }

        // If keys is passed in as an array, remove as bulk to save on queries
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $this->user->removePreferences($key);
            }
        } else {
            $this->user->removePreferences($keys);
        }
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
        return isset($this->user) ? $this->user : null;
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
        $this->targetService->setUser($user);
        return $this;
    }
}
