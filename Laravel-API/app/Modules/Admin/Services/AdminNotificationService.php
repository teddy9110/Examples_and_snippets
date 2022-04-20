<?php

namespace Rhf\Modules\Admin\Services;

use Carbon\Carbon;
use Exception;
use Rhf\Modules\Notifications\Models\Notifications;
use Rhf\Modules\Notifications\Services\NotificationService;

class AdminNotificationService
{
    private $notificationService;
    protected $notification = null;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getNotification()
    {
        if (!isset($this->notification)) {
            throw new Exception('Notification is not set');
        }
        return $this->notification;
    }

    public function setNotification(Notifications $notification)
    {
        $this->notification = $notification;
        return $this;
    }

    /**
     * Create a notification
     *
     * @param array $data
     * @return Notifications
     */
    public function createNotification(array $data)
    {
        $notification = new Notifications();

        $this->setNotification($notification);
        $this->updateNotification($data);
        return $notification;
    }

    /**
     * Update a notification
     *
     * @param array $data
     * @throws Exception
     */
    public function updateNotification(array $data)
    {
        $notification = $this->getNotification();

        foreach ($notification->getPlainKeys() as $key) {
            if (isset($data[$key])) {
                $notification[$key] = $data[$key];
            }
        }

        $notification->save();
    }

    /**
     * Send Notification immediately
     * and update the send_at to current time
     *
     * @throws Exception
     */
    public function sendNotification()
    {
        $notification = $this->getNotification();
        $result = $this->notificationService->sendTopicMessage(
            $notification->subtopic->slug,
            $notification->title,
            $notification->content,
            $notification->data
        );
        if ($result) {
            $this->updateNotification(
                [
                    'send_at' => Carbon::now()
                ]
            );
        }
    }
}
