<?php

namespace Rhf\Modules\Notifications\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Rhf\Modules\Notifications\Models\Topics;
use Rhf\Modules\User\Models\UserDevices;
use Rhf\Modules\User\Models\UserNotificationPreferences;

class NotificationService
{
    private $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    /**
     * Send message to specific topic
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @param null $topic
     * @return mixed
     */
    public function sendTopicMessage(
        $topic,
        string $title,
        string $body,
        array $data = null,
        $image = null,
        $link = null
    ) {
        $message = CloudMessage::fromArray(
            [
                'topic' => $topic,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => $data,
            ]
        );

        $send = $this->messaging->send($message);
        return $send;
    }

    /**
     * Send a message to a particular device
     * @param $token
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @param null $image
     * @param null $link
     * @return mixed
     */
    public function sendDeviceMessage(
        $token,
        string $title,
        string $body,
        array $data = null,
        $image = null,
        $link = null
    ) {
        if ($this->validateRegistrationToken($token)) {
            $message = CloudMessage::fromArray(
                [
                    'token' => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data' => $data,
                ]
            );

            $send = $this->messaging->send($message);
            return $send;
        }
        return false;
    }


    /**
     * subscribe multiple tokens to a single topic
     * @param $topic
     * @param array $token
     */
    public function subscribeTo($topic, array $tokens)
    {
        foreach ($tokens as $token) {
            if ($this->validateRegistrationToken($token)) {
                $result = $this->messaging->subscribeToTopic($topic, $token);
                return $result;
            }
        }
    }

    /**
     * unsubscribe multiple tokens from a single topic
     * @param $topic
     * @param array $token
     * @return mixed
     */
    public function unsubscribeFrom($topic, array $tokens)
    {
        foreach ($tokens as $token) {
            if ($this->validateRegistrationToken($token)) {
                $result = $this->messaging->unsubscribeFromTopic($topic, $token);
                return $result;
            }
        }
    }

    /**
     * Check the topics a user is subscribed to
     * returns a list an object of the token details
     *
     * @param $token
     */
    public function checkSubscription($token)
    {
        try {
            $appInstance = $this->messaging->getAppInstance($token);

            /** @var \Kreait\Firebase\Messaging\TopicSubscriptions $subscriptions */
            $subscriptions = $appInstance->topicSubscriptions();

            return $subscriptions;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * If the user has a device token or topics
     * subscribe all users to new topics as they are created
     * batches of 1000
     * @param $topic
     */
    public function subscribeAllUsersToTopic($topic)
    {
        $query = UserNotificationPreferences::where('device_ids', '!=', '[]')
            ->orWhere('topics_ids', '!=', '[]')
            ->get();

        $tokens = [];
        foreach ($query as $user) {
            foreach ($user->device_ids as $device) {
                $deviceId = UserDevices::findOrFail($device)->firebase_id;
                if ($this->validateRegistrationToken($deviceId)) {
                    array_push($tokens, $deviceId);
                }
            }
        }
        foreach (array_chunk($tokens, 1000) as $token) {
            $this->subscribeTo($topic, $token);
        }
    }

    /**
     * Unsubscribe all users from all topics as a topic is deleted
     * batches of 1000
     * @param $topic
     */
    public function unsubscribeAllUsersFromTopic($topic)
    {
        $id = Topics::where('slug', $topic)->first()->id;
        $query = UserNotificationPreferences::where('device_ids', '!=', '[]')
            ->orWhere('topics_ids', 'LIKE', $id)
            ->get();

        $tokens = [];
        foreach ($query as $user) {
            foreach ($user->device_ids as $device) {
                $deviceId = UserDevices::findOrFail($device)->firebase_id;
                if ($this->validateRegistrationToken($deviceId)) {
                    array_push($tokens, $deviceId);
                }
            }
        }
        foreach (array_chunk($tokens, 1000) as $token) {
            $this->unsubscribeFrom($topic, $token);
        }
    }


    /**
     * Check if a token is valid
     * @param $token
     * @return bool
     */
    public function validateRegistrationToken($token): bool
    {
        try {
            $appInstance = $this->messaging->getAppInstance($token);

            /** @var \Kreait\Firebase\Messaging\RegistrationToken $registration */
            $registration = $appInstance->registrationToken();

            $result = $token instanceof $registration ? $token : $registration->value($token);

            if ($token === $result) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
