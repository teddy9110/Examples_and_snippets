<?php

namespace Rhf\Modules\Notifications\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Notifications\Resources\NotificationPreferenceResource;
use Rhf\Modules\Notifications\Resources\NotificationPreferencesResource;
use Rhf\Modules\Notifications\Requests\NotificationPreferencesRequest;
use Rhf\Modules\User\Services\UserPushNotificationService;
use Sentry\Laravel\Facade as Sentry;

class NotificationController extends Controller
{
    /**
     * PUT - Setups User Notification Preferences
     * Creates row if none, else updates the row with new token if does not match existing.
     *
     * @param Request $request
     */
    public function userTokenInitialization(NotificationPreferencesRequest $request)
    {
        try {
            if (!empty($deviceId = $request->input('device_token'))) {
                $userNotificationPreferences = new UserPushNotificationService(auth('api')->user());
                $userNotificationPreferences->createNotificationPreferences($deviceId, api_version());
                return response()->json([
                    'message' => 'Token added successfully'
                ], 200);
            }
        } catch (FitnessBadRequestException $e) {
            Sentry::captureException('PN: Token Initialization' . $e->getCode() . ':' . $e->getMessage());
            throw new FitnessBadRequestException(
                'Error: Failed to create user notification preferences. Please try again'
            );
        }
    }

    /**
     * GET - Returns user notification preferences
     * PATCH - Updates user Notification preferences and returns updated preferences
     *
     * @param NotificationPreferencesRequest $request
     */
    public function userNotificationPreferences(NotificationPreferencesRequest $request)
    {
        try {
            $userNotificationPreferences = new UserPushNotificationService(auth('api')->user());
            $apiVersion = api_version();
            if (!empty($request->input('notifications'))) {
                $data = $request->input('notifications');
                if (api_version() >= 20210310) {
                    collect($data)->map(
                        function ($item, $key) use ($apiVersion, $userNotificationPreferences) {
                            $userNotificationPreferences->updateNotificationPreference(
                                $item['slug'],
                                $item['enabled'],
                                $apiVersion
                            );
                        }
                    );
                } else {
                    collect($data)->map(
                        function ($item, $key) use ($apiVersion, $userNotificationPreferences) {
                            $userNotificationPreferences->updateNotificationPreference(
                                $item['slug'],
                                $item['enabled'],
                                $apiVersion
                            );
                        }
                    );
                }
            }
        } catch (FitnessBadRequestException $e) {
            Sentry::captureException('PN: Update Preferences' . $e->getCode() . ':' . $e->getMessage());
            throw new FitnessBadRequestException(
                'Error: Failed to update User Notification Preferences. Please try again'
            );
        }
        if (api_version() >= 20210310) {
            return new NotificationPreferencesResource(
                $userNotificationPreferences->getNotificationPreferences($apiVersion)
            );
        } else {
            return new NotificationPreferenceResource($this->userNotificationPreferences->getNotificationPreferences());
        }
    }

    /**
     * If the user logs out of a device
     * gets the device token and unsubscribes that token from all topics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTokenLogout(NotificationPreferencesRequest $request)
    {
        try {
            $userNotificationPreferences = new UserPushNotificationService(auth('api')->user());
            if ($request->has('device_token')) {
                $user = auth('api')->user();
                if (!is_null($user->notificationPreferences)) {
                    $userNotificationPreferences->removeTokenFromUserNotificationPreferences(
                        $request->get('device_token'),
                        api_version()
                    );
                }
            }
        } catch (FitnessBadRequestException $e) {
            Sentry::captureException('PN: User Logout' . $e->getCode() . ':' . $e->getMessage());
            throw new FitnessBadRequestException(
                'Error: Unable to unsubscribe token. Please try again'
            );
        }
        return response()->noContent();
    }
}
