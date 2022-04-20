<?php

namespace Rhf\Modules\User\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\Product\Models\ProductBundles;
use Rhf\Modules\Product\Models\WorkoutProductBundleType;
use Rhf\Modules\System\Exceptions\DisplayedErrorException;
use Rhf\Modules\User\Models\UserSubscriptions;
use Rhf\Modules\User\Requests\ProgressPictureConsentRequest;
use Rhf\Modules\User\Requests\QuestionnaireRequest;
use Rhf\Modules\User\Resources\PeriodTrackingResource;
use Rhf\Modules\User\Services\UserQuestionnaireService;
use Rhf\Modules\User\Services\UserService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\User\Requests\UserDetailsRequest;
use Rhf\Modules\User\Requests\ManagePendingUserRequest;
use Rhf\Modules\User\Resources\UserResource;
use Rhf\Modules\User\Services\TargetService;
use Rhf\Modules\User\Resources\UserTargetsResource;
use Rhf\Modules\User\Models\UserProgressPicture;
use Rhf\Modules\User\Models\UserProgress;
use Rhf\Modules\User\Requests\UserProgressRequest;
use Rhf\Modules\User\Resources\UserProgressResource;
use Rhf\Modules\User\Services\UserFileService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    /**
     * @var UserFileService
     */
    protected $userFileService;

    /**
     * Create a new UserController instance.
     *
     * @param UserFileService $userFileService
     */
    public function __construct(UserFileService $userFileService)
    {
        $this->userFileService = $userFileService;
    }

    /**
     * Create a pending user in the system that can be tested through onboarding.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPendingUser(Request $request)
    {
        // Check for existing user
        if (User::where('email', '=', $request->get('email'))->count() > 0) {
            throw new FitnessBadRequestException('Email address already exists, have you previously registered?' .
                ' Try recovering your password.');
        }

        $user = User::create([
            'first_name'    => '',
            'surname'       => '',
            'email'         => $request->get('email'),
            'password'      => bcrypt(Str::random()),
            'expiry_date'   => date('Y-m-d 12:00:00', strtotime('+1 year')),
        ]);

        $user->preferences()->create();
        $user->workoutPreferences()->create();

        return response()->json(['status' => 'success']);
    }

    /**
     * Retrieve user details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        try {
            // Update the user goals if necessary
            if ($user->needsGoals()) {
                $userService = new UserService();
                $userService->setUser($user)->updateGoals();
            }

            $resource = new UserResource($user);
        } catch (Exception $e) {
            app('sentry')->captureException($e);
            throw new FitnessBadRequestException(
                'Sorry, we are unable to retrieve user details. Please try again later.'
            );
        }

        $activityLogCount = $user->weekActivityLog()->count();
        $remainingProfileUpdates = config('user.profile_updates') - $activityLogCount;

        return response()->json([
            'remaining_updates' => $remainingProfileUpdates,
            'status' => 'success',
            'data' => $resource
        ]);
    }

    /**
     * Retrieve user targets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTargets(Request $request)
    {
        $targetService = new TargetService();

        if ($targetService->setUser(auth('api')->user())->canCalculateGoals([])) {
            $resource = new UserTargetsResource(auth('api')->user());
        } else {
            throw new FitnessPreconditionException(
                'Sorry, we are unable to retrieve your targets. Please contact Team RH Support.'
            );
        }

        return response()->json(['status' => 'success', 'data' => $resource]);
    }

    /**
     * Create a pending user in the system that can be tested through onboarding.
     *
     * ASHBOURNE CALL
     * NEVER CHANGE THIS UNLESS THERE IS A GOOD REASON TO DO SO.
     * Used to create a user account when they sign up for a life plan membership on store
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function managePendingUser(ManagePendingUserRequest $request)
    {
        try {
            // Check for existing user
            $user = User::where('email', '=', $request->get('email'))->first();
            if ($user) {
                $user->expiry_date = date('Y-m-d 23:59:59', strtotime($request->get('expires')));
                $user->paid = $request->get('paid');
                $user->save();
            } else {
                $user = User::create([
                    'first_name'    => $request->get('first_name'),
                    'surname'       => $request->get('surname'),
                    'email'         => $request->get('email'),
                    'paid'          => $request->get('paid'),
                    'password'      => bcrypt(Str::random(10)),
                    'expiry_date'   => date('Y-m-d 23:59:59', strtotime($request->get('expires'))),
                ]);

                $user->preferences()->create();
                $user->workoutPreferences()->create();
            }

            if ($user) {
                //create user subscription
                $this->userSubscriptionInformation($user, 'monthly');
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: unable to manage user. Please try again later.');
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Create a pending user in the system that can be tested through onboarding.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDetails(UserDetailsRequest $request)
    {
        try {
            $data = $request->validated();
            $apiVersion = api_version();

            if ($request->has('exercise_location')) {
                $loc = $request->input('exercise_location');

                // If api-version header is not set, use the old lookup (by title)
                if (!$apiVersion) {
                    $prop = 'title';
                } else {
                    $prop = 'slug';
                    $loc = strtolower($loc);
                }


                $data['exercise_location_id'] = ExerciseLocation::where($prop, '=', $loc)->firstOrFail()->id;
                unset($data['exercise_location']);
            }

            if ($request->has('exercise_frequency')) {
                $freq = $request->input('exercise_frequency');

                // If api-version header is not set, use the old lookup (by amount)
                if (!$apiVersion) {
                    $prop = 'amount';
                } else {
                    $prop = 'slug';
                    $freq = strtolower($freq);
                }

                $data['exercise_frequency_id'] = ExerciseFrequency::where($prop, '=', $freq)->firstOrFail()->id;
                unset($data['exercise_frequency']);
            }

            // Override exercise_level_id if exercise_level is set
            if ($request->has('exercise_level')) {
                $level = strtolower($request->input('exercise_level'));
                $data['exercise_level_id'] = ExerciseLevel::where('slug', '=', $level)->firstOrFail()->id;
                unset($data['exercise_level']);
            }

            $userService = new UserService();
            $userService->setUser(auth('api')->user())->update($data);
        } catch (DisplayedErrorException $e) {
            throw new FitnessHttpException($e->getMessage(), 423);
        } catch (FitnessHttpException $e) {
            throw new FitnessHttpException($e->getMessage(), $e->getStatusCode());
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                $e->getMessage() . 'Error: unable to update user. Please try again later.'
            );
        }

        // User has 15 attempts so minus the above number from the amount of activity logs to get the remaining count
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $activityLogCount = $user->fresh()->weekActivityLog()->count();
        $remainingProfileUpdates = config('user.profile_updates') - $activityLogCount;

        return response()->json(['status' => 'success', 'remaining_updates' => $remainingProfileUpdates]);
    }

    /**
     * Create a new set of progress pictures saved against the request user
     *
     * @param UserProgressRequest $request
     * @return UserProgressResource
     * @throws Exception
     */
    public function createProgressPictures(UserProgressRequest $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $attributes = $request->all();
        $userProgress = null;

        DB::Transaction(function () use ($user, $attributes, &$userProgress) {
            // Get the users latest weight from activity if it exists,
            // otherwise get the users start weight stored in the user meta,
            // if neither exist, default to 0
            $weightValue = 0;

            $activity = $user->activity()->latestWeightActivity();

            if (isset($attributes['weight'])) {
                $weightValue = $attributes['weight'];
            } elseif ($activity->exists()) {
                $weightValue = $activity->first()->value;
            } elseif ($user->hasPreference('start_weight')) {
                $weightValue = $user->getPreference('start_weight');
            }

            $attributes['date'] = !array_key_exists('date', $attributes) ?
                now()->format('Y-m-d') :
                Carbon::parse($attributes['date'])->format('Y-m-d');

            $userProgress = UserProgress::create([
                'user_id' => $user->id,
                'weight_value' => $weightValue,
                'date' => $attributes['date']
            ]);

            foreach ($attributes['items'] as $attribute) {
                $attribute['user_progress_id'] = $userProgress->id;
                // Progress pictures should always be private
                $attribute['public'] = false;
                $attribute['date'] = $attributes['date'];

                $data = array_replace(
                    $attribute,
                    $this->userFileService->createFromUpload(
                        $attribute['file'],
                        $this->userFileService->generatePath(auth('api')->user()->id),
                        $attribute['public']
                    )
                );

                UserProgressPicture::create($data);
            }
        });

        if ($userProgress != null) {
            return new UserProgressResource($userProgress);
        }

        throw new BadRequestHttpException();
    }

    public function editProgressPicture($id, UserProgressRequest $request)
    {
        $userProgress = UserProgress::findOrFail($id);
        try {
            if ($request->validated()) {
                $weight_value = $request->has('weight') ? $request->input('weight') : $userProgress->weight_value;
                $userProgress->update([
                    'date' => $request->input('date'),
                    'weight_value' => $weight_value
                ]);
                UserProgressPicture::where('user_progress_id', $id)->update([
                    'date' => $request->input('date')
                ]);
            }
            return new UserProgressResource($userProgress);
        } catch (Exception $e) {
            throw new Exception('Sorry, there was a problem with this request. Please try again later');
        }
    }

    public function getProgressPictures(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        $user->load('progress.progressPicture');

        $userProgress = $user->progress()->orderBy('id', 'desc')->get();
        return UserProgressResource::collection($userProgress);
    }

    /**
     * Request from Dan
     * Ability to get a single progress picture via an id
     *
     * @param $id
     * @return UserProgressResource
     */
    public function getProgressPicture($id)
    {
        return new UserProgressResource(UserProgress::findOrFail($id));
    }

    public function deleteProgressPicture(Request $request, $id)
    {
        // Grab the pivot first
        $progressPicturePivot = UserProgress::where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $progressPictures = $progressPicturePivot->progressPicture->all();
        $userFileService = app(UserFileService::class);

        // Delete remotely off storage disk
        foreach ($progressPictures as $progressPicture) {
            $userFileService->delete($progressPicture);
        }

        try {
            // Delete the front and side images & pivot
            $progressPicturePivot->progressPicture()->delete();
            $progressPicturePivot->delete();
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, we are unable to delete this progress photo. Please try again later.'
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Consent to use of progress picture
     *
     * @param ProgressPictureConsentRequest $request
     *
     * @param string $type
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function consentProgress(ProgressPictureConsentRequest $request, string $type)
    {
        $user = auth('api')->user();
        $userService = new UserService();
        $userService->setUser($user);

        $userService->updatePreferences([
            'progress_picture_consent' => $type
        ]);

        return response(null, 204);
    }

    /**
     * Mark tutorial complete
     **/
    public function markTutorialComplete()
    {
        $user = auth('api')->user();
        $preferences = $user->preferences;
        $preferences->tutorial_complete = true;
        $preferences->save();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function onboardProductBundle()
    {
        //Returns user and preferences
        $user = auth('api')->user();

        // gets the suitable bundle based on preferences
        $bundleId = WorkoutProductBundleType::getBundleId(
            $user->preferences->exercise_location_id,
            $user->preferences->exercise_level_id,
            $user->preferences->exercise_frequency_id
        );

        return response()->json(
            [
                'status' => 'success',
                'data' => ProductBundles::find($bundleId)
            ]
        );
    }

    private function userSubscriptionInformation($user, $frequency)
    {
        $exists = UserSubscriptions::where('user_id', $user->id)
            ->where('email', $user->email)
            ->where('subscription_provider', 'directdebit')
            ->first();

        if ($exists) {
            $exists->update([
                'subscription_frequency' => $frequency,
                'expiry_date' => $user->expiry_date,
            ]);
            $exists->save();
        } else {
            UserSubscriptions::create([
              'user_id' => $user->id,
              'email' => $user->email,
              'subscription_provider' => 'directdebit',
              'subscription_frequency' => $frequency,
              'purchase_date' => now(),
              'expiry_date' => $user->expiry_date,
            ]);
        }
    }

    public function userAccountStatus(Request $request)
    {
        $request->validate([
           'email' => 'required|regex:/(.+)@(.+)\.(.+)/i'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->active == 0 && $user->needsGoals()) {
                return response()->json([
                    'status' => 'not_active'
                ]);
            }
            if ($user->needsGoals()) {
                return response()->json([
                    'status' => 'not_onboarded'
                ]);
            }
        }
        return response()->json([
            'status' => 'onboarded'
        ]);
    }

    /**
     * Check status of period tracking
     *
     * @return PeriodTrackingResource
     */
    public function getPeriodTrackingStatus()
    {
        $user = Auth::user()->preferences;
        return new PeriodTrackingResource($user);
    }

    /**
     * Set period tracking status, accepts true/false
     *
     * @param Request $request
     * @return PeriodTrackingResource
     */
    public function setPeriodTrackingStatus(Request $request)
    {
        $request->validate([
            'period_tracker' => 'required|boolean',
        ]);

        $user = Auth::user()->preferences;
        $user->update(['period_tracker' => $request->input('period_tracker')]);

        return new PeriodTrackingResource($user);
    }

    /**
     * User Questionnaire, creates a Zendesk ticket linked to the users account
     *
     * @param QuestionnaireRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userQuestionnaire(QuestionnaireRequest $request)
    {
        try {
            $uq = new UserQuestionnaireService(Auth::user());
            $create = $uq->createUserQuestionnaire($request->validated());
            if ($create) {
                return response()->json([
                    'message' =>
                        'Thank you, your answers were submitted successfully. Please check the helpdesk for an answer.'
                ], 200);
            }
            return response()->json([
                'message' => 'Sorry, there was an error. Please try again. '
            ], 400);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
