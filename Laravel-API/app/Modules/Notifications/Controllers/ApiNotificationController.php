<?php

namespace Rhf\Modules\Notifications\Controllers;

use Exception;
use Illuminate\Validation\ValidationException;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Notifications\Models\UserApiNotification;
use Rhf\Modules\Notifications\Requests\ApiNotificationsRequest;
use Rhf\Modules\Notifications\Resources\ApiNotificationsResource;
use Rhf\Modules\Notifications\Services\ApiNotificationService;

class ApiNotificationController extends Controller
{
    protected $apiNotificationService;

    public function __construct(ApiNotificationService $apiNotificationService)
    {
        $this->apiNotificationService = $apiNotificationService;
    }

    /**
     * Create notifications
     *
     * @param ApiNotificationsRequest $request
     * @return mixed
     */
    public function createNotification(ApiNotificationsRequest $request)
    {
        return $this->apiNotificationService->createNotification($request->validated());
    }

    /**
     * Get Notifications based on criteria passed in
     *
     * @param ApiNotificationsRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getNotifications(ApiNotificationsRequest $request)
    {
        if (!in_array(strtolower($request->input('platform')), ['ios', 'android'])) {
            throw ValidationException::withMessages([
                'platform' => 'Platform must be either ios or android.',
            ]);
        }
        $notifications = $this->apiNotificationService->getNotifications($request->validated());
        return ApiNotificationsResource::collection($notifications);
    }

    /**
     * acknowledge notifications by ID
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function acknowledgeNotification($id): \Illuminate\Http\Response
    {
        try {
            $this->apiNotificationService->handleNotification($id);
            return response()->noContent();
        } catch (FitnessBadRequestException $e) {
            throw new FitnessBadRequestException(
                'Sorry, unable to complete this request, please try later',
                $e->getMessage()
            );
        }
    }

    /**
     * Clear notifications - TESTING ROUTE
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = auth('api')->user()->id;
            $dismissedNotifications = UserApiNotification::where('user_id', $userId)->get();
            foreach ($dismissedNotifications as $notification) {
                $notification->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'You\'re welcome Dan'
            ]);
        } catch (Exception $e) {
            throw new FitnessBadRequestException(
                'No notifications to clear'
            );
        }
    }
}
