<?php

namespace Rhf\Modules\User\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Rhf\Modules\Activity\Models\ActivityNotes;
use Rhf\Modules\Notifications\Models\ApiNotification;
use Rhf\Modules\Notifications\Models\UserApiNotification;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\Subscription\Models\AppleSubscriptions;
use Rhf\Modules\Tags\Models\Tag;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Rhf\Modules\User\Services\UserService;
use Rhf\Modules\User\Notifications\CustomResetPasswordNotification;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\User\Decorators\HasSubscriptionDataAttribute;
use Rhf\Modules\Workout\Models\WorkoutPreference;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    use HasSubscriptionDataAttribute;
    use HasFactory;

    // TODO: Deprecate this
    protected $appends = ['exercise_location', 'exercise_frequency'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role', 'admin', 'active', 'paid', 'first_name', 'surname', 'email', 'password', 'mfp_state_token',
        'expiry_date','last_active', 'next_payment_date', 'staff_user', 'test_user',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'expiry_date', 'deleted_at', 'last_active', 'next_payment_date'
    ];

    // NOTE: 'schedule' is not in UserPreferences->getFillable(), therefore if it will not be update
    //       if fillables are used to iterate over props to update.
    protected array $workoutPreferenceProps = [
        'exercise_level_id',
        'exercise_location_id',
        'exercise_frequency_id',
        'schedule',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getWorkoutPreferenceProps(): array
    {
        return $this->workoutPreferenceProps;
    }

    /**
     *
     * RELATIONSHIPS
     *
     */

    /**
     * Relation to user preferences.
     */
    public function preferences()
    {
        return $this->hasOne(UserPreferences::class, 'user_id', 'id');
    }

    /**
     * Relation to activity log (system interactions, not exercises etc).
     */
    public function activityLog()
    {
        return $this->hasMany('Rhf\Modules\System\Models\ActivityLog', 'user_id', 'id');
    }

    /**
     * Relation to activity log.
     */
    public function activity()
    {
        return $this->hasMany('Rhf\Modules\Activity\Models\Activity', 'user_id', 'id');
    }

    /**
     * Relation to progress log.
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'user_id', 'id');
    }

    /**
     * Relation to staff notes.
     */
    public function staffNotes()
    {
        return $this->hasMany(StaffNote::class, 'user_id', 'id');
    }

    /**
     * Relation to user roles.
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'id');
    }

    /**
     * User's workout preferences.
     */
    public function workoutPreferences()
    {
        return $this->hasOne(WorkoutPreference::class);
    }

    /**
     * Recipes that the user has favourited.
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'user_favourite_recipes');
    }

    /**
     * Get User notification Preferences
     */
    public function notificationPreferences()
    {
        return $this->hasOne(UserNotificationPreferences::class, 'user_id', 'id');
    }

    /**
     * User device tokens.
     */
    public function deviceToken()
    {
        return $this->hasMany(UserDevices::class, 'user_id', 'id');
    }

    /**
     * User's last purchased subscription.
     */
    public function subscription()
    {
        return $this->hasOne(UserSubscriptions::class, 'user_id', 'id')->orderBy('purchase_date', 'desc');
    }

    /**
     * All user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscriptions::class, 'user_id', 'id')->orderBy('purchase_date', 'desc');
    }

    /**
     * Many to many for tags
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'user_tags');
    }

    /**
     * User's activity notes.
     */
    public function activityNotes()
    {
        return $this->hasMany(ActivityNotes::class, 'user_id', 'id');
    }

    public function appStoreReview()
    {
        return $this->hasOne(UserAppStoreReview::class, 'user_id', 'id');
    }

    /**
     * Get Apple subscription records for user.
     */
    public function apple()
    {
        return $this->hasManyThrough(
            AppleSubscriptions::class,
            UserSubscriptions::class,
            'user_id',
            'current_transaction_id',
            'id',
            'subscription_reference'
        );
    }

    /**
     * User questionnaire relation
     */
    public function questionnaire()
    {
        return $this->hasMany(UserQuestionnaire::class, 'user_id', 'id');
    }

    /**
     * API notifications that the user has received & dismissed.
     */
    public function apiNotifications()
    {
        return $this->hasManyThrough(
            ApiNotification::class,
            UserApiNotification::class,
            'user_id',
            'id',
            'id',
            'notification_id'
        );
    }

    /**
     *
     * ATTRIBUTES
     *
     */

    public function getExerciseFrequencyAttribute()
    {
        if ($this->hasPreference('exercise_frequency_id')) {
            return ExerciseFrequency::find($this->getPreference('exercise_frequency_id'));
        }
        return null;
    }

    public function getExerciseLocationAttribute()
    {
        if ($this->hasPreference('exercise_location_id')) {
            return ExerciseLocation::find($this->getPreference('exercise_location_id'));
        }
        return null;
    }

    public function getExerciseLevelAttribute()
    {
        if ($this->hasPreference('exercise_level_id')) {
            return ExerciseLevel::find($this->getPreference('exercise_level_id'));
        }
        return null;
    }

    /**
     * Return Name as an attribute
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->surname;
    }

    /**
     * Return the number of days a user has been a member for
     *
     * @return int
     */
    public function getMembershipDaysAttribute(): int
    {
        return now()->startOfDay()->diffInDays(Carbon::parse($this->created_at)->startOfDay()) + 1;
    }

    /**
     *
     * SCOPES
     *
     */

    /**
     * Only active users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', '=', 1);
    }

    /**
     * Only paid users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePaid($query)
    {
        return $query->where('paid', '=', 1)->where('expiry_date', '>', date('Y-m-d 00:00:01'));
    }

    /**
     * Only inactive users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('active', '=', 0);
    }

    /**
     * Filter result set by keyword search
     *
     * @param Builder $query
     * @param string $term
     * @return Builder
     */
    public function scopeSearch($query, $term)
    {
        // TODO - Test filters correctly on OR condition
        return $query->where(function ($query) use ($term) {
            $query->where('first_name', 'LIKE', '%' . $term['value'] . '%')
                ->orWhere('surname', 'LIKE', '%' . $term['value'] . '%')
                ->orWhere('email', 'LIKE', '%' . $term['value'] . '%');
        });
    }

    /**
     *
     * Scope a query to only a customer account
     *
     * @param $query
     * @return mixed
     */
    public function scopeCustomer($query)
    {
        return $query->where('role_id', 4);
    }

    /**
     *
     * Scope a query to only a customer account
     *
     * @param $query
     * @return mixed
     */
    public function scopeStaffAccount($query)
    {
        return $query->where('staff_user', true);
    }

      /**
     *
     * Scope a query to only a customer account
     *
     * @param $query
     * @return mixed
     */
    public function scopeCustomerAccount($query)
    {
        return $query->where('staff_user', false);
    }

    /**
     * Scope for Active/Paid/Customer
     * @param $query
     * @return mixed
     */
    public function scopeActivePaidCustomer($query)
    {
        return $query->whereActive(1)->wherePaid(1)->whereRoleId(4);
    }

    /**
     *
     * JWT auth
     *
     */

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     *
     * PREFERENCES
     *
     */

    /**
     * Return whether or not a specific preference is available for the current user.
     *
     * @param string key
     * @return bool
     */
    public function hasPreference($key)
    {
        if (in_array($key, $this->workoutPreferenceProps)) {
            return $this->getWorkoutPreference($key) != null;
        }
        return $this->preferences[$key] != null;
    }

    /**
     * Check if multiple preferences exist and are not null.
     *
     * @param array $keys
     * @return bool
     */
    public function hasPreferences(array $keys)
    {
        foreach ($keys as $key) {
            if (!$this->hasPreference($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return a user preference.
     *
     * @param $key
     * @param $default
     * @return
     */
    public function getPreference($key, $default = null)
    {
        if (in_array($key, $this->workoutPreferenceProps)) {
            return $this->getWorkoutPreference($key) ?? $default;
        }
        return $this->preferences[$key] ?? $default;
    }

    /**
     * Sets a user preference.
     *
     * @param string key
     * @param value
     * @return
     */
    public function setPreference($key, $value)
    {
        if (in_array($key, $this->workoutPreferenceProps)) {
            $this->setWorkoutPreference($key, $value);
        } else {
            $this->preferences[$key] = $value;
        }
    }

    /**
     * Removes a user preference.
     *
     * @param string key
     * @param value
     * @return
     */
    public function removePreferences($key)
    {
        if (in_array($key, $this->workoutPreferenceProps)) {
            $this->setWorkoutPreference($key, null);
        }
        $this->preferences[$key] = null;
    }

    /**
     * Get workout preferences, taking into account whether the user has migrated, workouts V3 feture is enabled
     * and if they are using the updated app version that supports workouts V3.
     *
     * If a user has migrated previously, but is using an app version that does not support workouts V3,
     * their V2 backup data will be used for getting workout preferences.
     *
     * @param mixed $key
     * @return mixed
     * @throws BindingResolutionException
     */
    private function getWorkoutPreference($key)
    {
        $v2Preferences = $this->workoutPreferences->data['workouts_v2_preferences'] ?? null;
        if (!empty($v2Preferences) && !workouts_v3_available()) {
            return $v2Preferences[$key];
        }

        return $this->workoutPreferences[$key];
    }

    /**
     * Set workout preferences, taking into account whether the user has migrated, workouts V3 feture is enabled
     * and if they are using the updated app version that supports workouts V3.
     *
     * If a user has migrated previously, but is using an app version that does not support workouts V3,
     * their V2 backup data will be used for setting workout preferences.
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     * @throws BindingResolutionException
     */
    private function setWorkoutPreference($key, $value)
    {
        $v2Preferences = $this->workoutPreferences->data['workouts_v2_preferences'] ?? null;
        if (!empty($v2Preferences) && !workouts_v3_available()) {
            $v2Preferences[$key] = $value;
            $data = $this->workoutPreferences->data;
            $data['workouts_v2_preferences'] = $v2Preferences;
            $this->workoutPreferences->data = $data;
        } else {
            $this->workoutPreferences[$key] = $value;
        }
    }

    /**
     *
     * HELPERS & UTILITIES
     *
     */

    /**
     * User's activity log from now to a week ago.
     */
    public function weekActivityLog()
    {
        return $this->activityLog()->where('action', '=', 'UserUpdateDetails')
            ->where('created_at', '>', Carbon::now()->subDays(7));
    }

    /**
     * Activate the user.
     *
     * @return self
     */
    public function activate()
    {
        $this->active = 1;
        $this->save();
        return $this;
    }

    /**
     * Deactivate the user.
     *
     * @return self
     */
    public function deactivate()
    {
        $this->active = 0;
        $this->save();
        return $this;
    }

    /**
     * Return whether or not the user has successfully connected MFP.
     *
     * @return bool
     */
    public function hasConnectedMfp()
    {
        return $this->hasPreference('mfp_authentication_code') && $this->hasPreference('mfp_access_token');
    }

    /**
     * Remove all the MFP fields within meta, essentially disconnecting MFP from the user
     * return whether the disconnect was successful of not
     *
     * @return bool
     * @throws Exception
     */
    public function removeMfp()
    {
        // Check that the user has the MFP keys stored in meta
        $keys = ['mfp_access_token', 'mfp_refresh_token', 'mfp_token_expires_at',
                 'mfp_user_id', 'mfp_authentication_code'];

        // Before calling to remove the MFP meta keys, make sure that the user is connected to MFP
        if ($this->hasConnectedMfp()) {
            // If the user is connected, remove all related MFP keys, and set the mfp_state_token to null on user
            $userService = new UserService();
            $userService->setUser($this);
            $userService->removePreferences($keys);
            $this->mfp_state_token = null;
            $this->preferences->save();
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Return if current user is admin or not.
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (in_array($this->role->slug, ['god', 'higher-admin', 'admin'])) {
            return true;
        }
        return false;
    }

    /**
     * Returns an array of user roles that the user can alter
     *
     * @return array
     */
    public function userRolePermissionScopes()
    {
        switch ($this->role->slug) {
            case 'god':
                return ['god', 'higher-admin', 'admin', 'customer'];
            case 'higher-admin':
                return ['admin', 'customer'];
            case 'admin':
                return ['customer'];
            default:
                return [];
        }
    }

    /**
     * Return if current user is active or not.
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->active != 1) {
            return false;
        }

        return !$this->isExpired();
    }

    /**
     * Return if current user is paid / subscribed or not.
     *
     * @return bool
     */
    public function isPaid()
    {
        if ($this->paid != 1) {
            return false;
        }

        return !$this->isExpired();
    }

    /**
     * Checks if a user is expired.
     *
     * #1
     * If the user has no expiry date set, so they have never subscribed.
     *
     * #2
     * If next_payment_date is not set, they most likely are not a Direct Debit payer, therefore
     * their expiry date would be correct.
     *
     * #3
     * If the user has next_payment_date set, they are a Direct Debit payer, therefore we need to compare
     * today's date to either next payment date or expiry date, whichever is greater.
     * This is required as often they would pay within the term they are paying for, currently up to 14 days
     * into it. We also need to add a padding of 5 days to that date, to account for delays in receiving
     * reconcilliation (successful collection) report.
     */
    public function isExpired()
    {
        // #1
        if (is_null($this->expiry_date)) {
            return true;
        }

        // #2
        if (is_null($this->next_payment_date)) {
            return $this->expiry_date->lessThan(now());
        }

        // #3
        $effectiveExpiryDate = null;
        $directDebitCollectionPadding = 5;
        $expiryDate = $this->expiry_date->startOfDay();
        $nextPaymentDate = $this->next_payment_date->startOfDay();

        if ($nextPaymentDate->greaterThan($expiryDate)) {
            $effectiveExpiryDate = $this->next_payment_date->addDays($directDebitCollectionPadding);
        } else {
            $diff = $expiryDate->diffInDays($nextPaymentDate);
            $effectiveExpiryDate = $this->expiry_date->copy();
            if ($diff < $directDebitCollectionPadding) {
                $effectiveExpiryDate = $effectiveExpiryDate->addDays($directDebitCollectionPadding - $diff);
            }
        }

        return $effectiveExpiryDate->lessThan(now());
    }

    /**
     * Check if the user has no goals set.
     *
     * @return bool
     */
    public function needsGoals()
    {
        // If preferences don't exist, we must create them
        if (!$this->preferences) {
            $this->preferences()->create();
            $this->refresh();
            return true;
        }

        if (!$this->hasPreference('daily_calorie_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_carbohydrate_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_fat_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_fiber_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_protein_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_step_goal')) {
            return true;
        }
        if (!$this->hasPreference('daily_water_goal')) {
            return true;
        }
        return false;
    }

    /**
     * Reset a users goals
     *
     * @return bool
     */
    public function resetGoals()
    {
        $this->preferences->daily_step_goal = null;
        $this->preferences->daily_calorie_goal = null;
        $this->preferences->daily_protein_goal = null;
        $this->preferences->daily_carbohydrate_goal = null;
        $this->preferences->daily_fat_goal = null;
        $this->preferences->daily_fiber_goal = null;

        return $this->preferences->save();
    }

    /**
     * Issue a new state token for the MFP auth process.
     *
     * @return string
     */
    public function refreshMfpStateToken()
    {
        $this->mfp_state_token = Str::random(32);
        $this->save();
        return $this->mfp_state_token;
    }

    /**
     * Send the password reset email.
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /**
     * Update the user's password.
     *
     * @param string
     * @return self
     */
    public function updatePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();

        return $this;
    }

    /**
     * Checks if User has access to $permissions.
     */
    public function hasAccess(array $permissions): bool
    {
        return $this->role->hasAccess($permissions) ? true : false;
    }

    /**
     * Checks if the user belongs to role.
     */
    public function inRole(string $roleSlug)
    {
        return $this->role->slug == $roleSlug;
    }

    /*
     * Unlink mfp for a user
     */
    public function unlinkMfp()
    {
        $this->setPreference('mfp_access_token', null);
        $this->setPreference('mfp_refresh_token', null);
        $this->setPreference('mfp_token_expires_at', null);
        $this->setPreference('mfp_user_id', null);
        $this->setPreference('mfp_authentication_code', null);
        $this->preferences->save();
    }

    /*
     * Has the mfp token expired?
     */
    public function hasMfpExpired()
    {
        $expiresOn = $this->getPreference('mfp_token_expires_at', Carbon::now()->timestamp);
        return Carbon::now()->addMinute(1) > Carbon::createFromTimestamp($expiresOn);
    }
}
