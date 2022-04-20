<?php

namespace Rhf\Modules\Notifications\Actions;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Rhf\Modules\System\Services\SendInBlueService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Workout\Models\ExerciseFrequency;
use Rhf\Modules\Workout\Models\ExerciseLocation;
use Sentry\Severity;
use Throwable;

use function Sentry\captureMessage;

class NotificationActions
{
    /**
     * Sends email to user with updated direct debit terms and conditions.
     */
    public function ashbourneToSmartDebit()
    {
        $user = Auth::user();

        try {
            $sendInBlue = new SendInBlueService();
            /** @var $user \Rhf\Modules\User\Models\User */
            $user = auth('api')->user();

            $sendInBlue->sendDirectDebitNotification(
                [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                [
                    'name' => $user->name,
                ]
            );
        } catch (Throwable | Exception $e) {
            $body = '<p>' . $user->name . ' (' . $user->email . ') did not get direct debit email due to:<br/>'
                . $e->getMessage() . '</p>';

            Log::error('Direct Debit provider change email not sent.');
            Log::error($e);

            // Send email if error from send in blue side to rectify with correct email
            Mail::send(array(), array(), function ($message) use ($body) {
                $message->to([
                    'david.murray@teamrhfitness.com',
                    'kaspars.zarinovs@teamrhfitness.com',
                ])
                ->subject('Direct Debit Email not sent')
                ->setBody($body, 'text/html');
            });
            captureMessage(
                'Direct Debit Email Failure. ' . $e->getMessage() . ':' . $e->getCode(),
                Severity::warning()
            );
        }
    }

    public function workoutsV2toV3Migration()
    {
        /** @var User $user */
        $user = Auth::user();

        if (is_null($user->workoutPreferences)) {
            $user->workoutPreferences()->create([
                'exercise_frequency_id' => $user->preferences->exercise_frequency_id,
                'exercise_level_id' => $user->preferences->exercise_level_id,
                'exercise_location_id' => $user->preferences->exercise_location_id,
            ]);
        }

        $frequencyId = $user->getPreference('exercise_frequency_id');
        $locationId = $user->getPreference('exercise_location_id');

        if (!is_null($frequencyId) && !is_null($locationId)) {
            $backup = [
                'schedule' => $user->getPreference('schedule'),
                'exercise_frequency_id' => $frequencyId,
                'exercise_level_id' => $user->getPreference('exercise_level_id'),
                'exercise_location_id' => $locationId,
            ];

            $location = ExerciseLocation::find($locationId);
            $frequency = ExerciseFrequency::find($frequencyId);

            if (
                $location->slug == ExerciseLocation::SLUG_HOME
                && $frequency->slug == ExerciseFrequency::SLUG_6
            ) {
                $frequencyId = ExerciseFrequency::where('slug', ExerciseFrequency::SLUG_5)->first()->id;
            }

            $user->workoutPreferences->update([
                'schedule' => null,
                'exercise_frequency_id' => $frequencyId,
                'exercise_level_id' => null,
                'exercise_location_id' => $locationId,
                'data' => [
                    'workouts_v2_preferences' => $backup,
                ],
            ]);
        }
    }
}
