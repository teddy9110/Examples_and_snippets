<?php

namespace Rhf\Modules\Admin\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\Admin\Requests\AdminNotificationRequest;
use Rhf\Modules\Admin\Resources\AdminNotificationResource;
use Rhf\Modules\Admin\Services\AdminNotificationService;
use Rhf\Modules\Notifications\Models\Notifications;
use Rhf\Modules\Notifications\Services\NotificationService;

class AdminNotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    private $notificationService;
    private $adminNotificationService;

    public function __construct(
        NotificationService $notificationService,
        AdminNotificationService $adminNotificationService
    ) {
        $this->notificationService = $notificationService;
        $this->adminNotificationService = $adminNotificationService;
    }

    /**
     * Get paginated notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filter = Arr::wrap($request->input('filter', []));

        $query = Notifications::query()->with('topic')->orderBy($orderBy, $orderDirection);

        return AdminNotificationResource::collection($query->paginate($perPage));
    }

    /**
     * Return a Notification
     * @param $id
     * @return AdminNotificationResource
     */
    public function showNotification($id)
    {
        $notification = Notifications::findOrFail($id);
        return new AdminNotificationResource($notification);
    }

    /**
     * Store notification
     *
     * @param AdminNotificationRequest $request
     * @return AdminNotificationResource
     */
    public function createNotification(AdminNotificationRequest $request)
    {
        $notification = $this->adminNotificationService->createNotification($request->validated());
        if ($notification && $request->input('send_now') === true) {
            $this->adminNotificationService->setNotification($notification);
            $this->adminNotificationService->sendNotification();
        }

        return new AdminNotificationResource($notification);
    }

    /**
     * Update a notification
     *
     * @param $id
     * @param AdminNotificationRequest $request
     * @return AdminNotificationResource
     */
    public function updateNotification($id, AdminNotificationRequest $request)
    {
        $notification = Notifications::findOrFail($id);

        $this->adminNotificationService->setNotification($notification);
        $this->adminNotificationService->updateNotification($request->validated());

        if ($notification && $request->input('send_now') === true) {
            $this->adminNotificationService->sendNotification();
        }

        return new AdminNotificationResource($notification);
    }

    /**
     * Immediately send a notification to the topic in the ID
     *
     * @param $id
     */
    public function sendNow($id)
    {
        $notification = Notifications::findOrFail($id);
        $this->adminNotificationService->setNotification($notification);
        $this->adminNotificationService->sendNotification();
    }

    /**
     * Delete a record and unsubscribe everyone from the topic
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notifications::findOrFail($id);
        $notification->delete();
        return response()->noContent();
    }
}
