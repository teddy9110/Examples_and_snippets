<?php

namespace Rhf\Modules\User\Services;

use Rhf\Modules\Notifications\Models\SubTopics;
use Rhf\Modules\Notifications\Models\Topics;
use Rhf\Modules\Notifications\Services\NotificationService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserDevices;
use Sentry\Laravel\Facade as Sentry;

class UserPushNotificationService
{
    private $notificationService;
    private $user;

    public function __construct(User $user)
    {
        $this->notificationService = new NotificationService();
        $this->user = $user;
    }

    public function createNotificationPreferences($token, $apiVersion = 0)
    {
        $this->pruneOldDeviceIds();
        if ($apiVersion >= 20210310) {
            $subtopics = SubTopics::where('subscribe', 1)->where('active', 1)->get();

            if (is_null($this->user->notificationPreferences)) {
                $subtopicArray = [];
                foreach ($subtopics as $subTopic) {
                    array_push($subtopicArray, $subTopic->id);
                    $this->notificationService->subscribeTo($subTopic->slug, [$token]);
                }
                $this->createNotificationPrefs($token, [], $subtopicArray);
            } else {
                $this->updateDeviceIds($token, $apiVersion);
            }
        } else {
            $topics = Topics::all();
            //create if doesn't exist
            if (is_null($this->user->notificationPreferences)) {
                $topicArray = [];
                foreach ($topics as $topic) {
                    array_push($topicArray, $topic->id);
                    $this->notificationService->subscribeTo($topic->slug, [$token]);
                }
                $this->createNotificationPrefs($token, $topicArray, []);
            } else {
                $this->updateDeviceIds($token, $apiVersion);
            }
        }
    }

    /**
     * @param $token
     * @param array $subtopicArray
     */
    public function createNotificationPrefs($token, array $topics, array $subtopicArray): void
    {
        $addTokenToUser = $this->user->deviceToken()->create(
            [
                'user_id' => $this->user->id,
                'firebase_id' => $token
            ]
        );

        $this->user->notificationPreferences()->create(
            [
                'user_id' => $this->user->id,
                'device_ids' => [$addTokenToUser->id],
                'topics_ids' => $topics,
                'subtopics_ids' => $subtopicArray
            ]
        );
    }

    /**
     * @param $token
     * @return array
     */
    public function updateDeviceIds($token, $apiVersion): array
    {
        $devices = $this->user->deviceToken->pluck('id')->toArray();
        $tokens = $this->user->deviceToken->pluck('firebase_id');

        if (!$tokens->contains($token)) {
            $addTokenToUser = $this->user->deviceToken()->create(
                [
                    'user_id' => $this->user->id,
                    'firebase_id' => $token
                ]
            );

            array_push($devices, $addTokenToUser->id);

            if ($apiVersion >= 20210310) {
                if (!empty($this->user->notificationPreferences->topics_ids)) {
                    $this->updateExistingUsersToSubtopic($token);
                } else {
                    foreach ($this->user->notificationPreferences->subtopic_slug as $slug) {
                        $this->notificationService->subscribeTo($slug, [$token]);
                    }
                }
            } else {
                foreach ($this->user->notificationPreferences->slug as $slug) {
                    $this->notificationService->subscribeTo($slug, [$token]);
                }
            }
        }

        $this->user->notificationPreferences->update(
            [
                'device_ids' => value($devices)
            ]
        );
        return $devices;
    }

    /**
     * @param $token
     * @return array
     */
    public function updateExistingUsersToSubtopic($token): array
    {
        $subtopics = SubTopics::where('subscribe', 1)->where('active', 1)->get();

        $subtopicArray = [];
        foreach ($subtopics as $subTopic) {
            array_push($subtopicArray, $subTopic->id);
            $this->notificationService->subscribeTo($subTopic->slug, [$token]);
        }
        $this->user->notificationPreferences->update(
            [
                'topics_ids' => [],
                'subtopics_ids' => $subtopicArray
            ]
        );
        return $subtopicArray;
    }

    public function getNotificationPreferences($apiVersion = 0)
    {
        $topics = Topics::all();

        $subscribed = [];
        if (!is_null($this->user->notificationPreferences)) {
            if ($apiVersion >= 20210310) {
                foreach ($topics as $topic) {
                    $category = strtolower(str_replace(' ', '_', $topic['category']));
                    $subscribed[$category] = [
                        'title' => $topic->category,
                        'description' => $topic->description,
                    ];
                }

                //migrate users from old to new if updated app and retrieve preferences
                if (!empty($this->user->notificationPreferences->topics_ids)) {
                    $this->migrateUsersToSubtopics();
                }

                $subtopics = SubTopics::with('topic')->where('active', 1)->get();
                $preferencesSubtopics = $this->user->notificationPreferences->subtopics_ids;

                foreach ($subtopics as $sub) {
                    $category = strtolower(str_replace(' ', '_', $sub->topic->category));

                    $subscribed[$category]['topics'][] = [
                        'title' => $sub->title,
                        'description' => $sub->description,
                        'slug' => $sub->slug,
                        'enabled' => !is_null($preferencesSubtopics) ?
                            in_array($sub->id, $preferencesSubtopics) :
                            false,
                    ];
                }
            } else {
                foreach ($topics as $topic) {
                    $category = strtolower(str_replace(' ', '_', $topic['category']));
                    $subscribed[$category][$topic->slug] = in_array(
                        $topic->id,
                        $this->user->notificationPreferences->topics_ids
                    );
                }
            }
        }

        return $subscribed;
    }

    /**
     * @return array
     */
    public function migrateUsersToSubtopics(): array
    {
        $topics = $this->user->notificationPreferences->topics_ids;
        $deviceIds = $this->user->notificationPreferences->device_ids;

        $tokens = UserDevices::whereIn('id', $deviceIds)->pluck('firebase_id')->toArray();
        $subtopics = SubTopics::where('subscribe', 1)->where('active', 1)->whereIn('topic_id', $topics)->get();

        $subtopicArray = [];
        foreach ($subtopics as $subtopic) {
            array_push($subtopicArray, $subtopic->id);
            $this->notificationService->subscribeTo($subtopic->slug, $tokens);
        }

        $this->user->notificationPreferences->update(
            [
                'topics_ids' => [],
                'subtopics_ids' => $subtopicArray
            ]
        );
        return $subtopicArray;
    }

    public function updateNotificationPreference($slug, $value, $apiVersion = 0)
    {
        $deviceIds = $this->user->notificationPreferences->device_ids;
        $tokens = UserDevices::whereIn('id', $deviceIds)->pluck('firebase_id')->toArray();

        if ($apiVersion >= 20210310 && !is_null($this->user->notificationPreferences->subtopics_ids)) {
            $topic = SubTopics::where('slug', $slug)->first();
            if ($topic) {
                $id = $topic->id;
                $existingTopics = $this->user->notificationPreferences->subtopics_ids;

                $updatedTopics = $this->updateExistingTopics($value, $id, $existingTopics, $slug, $tokens);

                $this->user->notificationPreferences->update(
                    [
                        'subtopics_ids' => array_values($updatedTopics)
                    ]
                );
            }
        } elseif (!empty($this->user->notificationPreferences->topics_ids)) {
            $topic = Topics::where('slug', $slug)->first();
            if ($topic) {
                $id = $topic->id;
                $existingTopics = $this->user->notificationPreferences->topics_ids;

                $updatedTopics = $this->updateExistingTopics($value, $id, $existingTopics, $slug, $tokens);

                $this->user->notificationPreferences->update(
                    [
                        'topics_ids' => array_values($updatedTopics)
                    ]
                );
            }
        }
    }

    /**
     * @param $value
     * @param $id
     * @param $existingTopics
     * @param $slug
     * @param $tokens
     * @return array
     */
    public function updateExistingTopics($value, $id, $existingTopics, $slug, $tokens): array
    {
        //add topics not present in the array if value is true
        if ($value && !in_array($id, $existingTopics)) {
            array_push($existingTopics, $id);
            //Subscribe user to topic with all present tokens
            $this->notificationService->subscribeTo($slug, $tokens);
        } elseif ($value == false && in_array($id, $existingTopics)) {
            //remove value if they are set to false and are subscribed
            unset($existingTopics[array_search($id, $existingTopics)]);
            //unsubscribe user to topic with all present tokens
            $this->notificationService->unsubscribeFrom($slug, $tokens);
        }
        return $existingTopics;
    }

    public function removeTokenFromUserNotificationPreferences($token, $apiVersion)
    {
        $existingTokens = $this->user->notificationPreferences->device_ids;
        $this->unsubscribeUserFromTopicWithToken($token);
        $userDeviceToken = UserDevices::where('firebase_id', $token)->firstOrFail();
        if (in_array($userDeviceToken->id, $existingTokens)) {
            unset($existingTokens[array_search($userDeviceToken->id, $existingTokens)]);
            $this->user->notificationPreferences->update(
                [
                    'device_ids' => array_values($existingTokens)
                ]
            );
            $userDeviceToken->delete();
        }
    }

    /**
     * Remove Old Device ID's
     * where user has more than 10 id's added
     *
     * @return void
     */
    private function pruneOldDeviceIds()
    {
        if (!$this->user->notificationPreferences) {
            return;
        }
        $deviceIds = $this->user->notificationPreferences->device_ids;
        $toRemove = [];
        if (count($deviceIds) > 9) {
            $toRemove = array_slice($deviceIds, 0, -9, true);
        }
        $this->removeOldDeviceIds($toRemove);
    }

    /**
     * Loop to remove old Id's
     * and unsubscribe Token from Topic
     *
     * @param array $deviceIds
     * @return void
     */
    private function removeOldDeviceIds(array $deviceIds)
    {
        try {
            foreach ($deviceIds as $key => $value) {
                $device = UserDevices::where('id', $value)->first();
                $this->unsubscribeUserFromTopicWithToken($device->firebase_id);
                $device->delete();
            }
        } catch (\Throwable $e) {
            Sentry::captureException($e);
        }
    }

    /**
     * @param $notificationPreferences
     * @param $token
     * @return void
     */
    private function unsubscribeUserFromTopicWithToken($token): void
    {
        $existingTopics = $this->user->notificationPreferences->topics_ids;
        $existingSubTopics = $this->user->notificationPreferences->subtopics_ids;

        if (!empty($existingSubTopics)) {
            foreach ($existingSubTopics as $topics) {
                $slug = SubTopics::where('id', $topics)->firstOrFail()->slug;
                $this->notificationService->unsubscribeFrom($slug, [$token]);
            }
        } else {
            foreach ($existingTopics as $topics) {
                $slug = Topics::where('id', $topics)->firstOrFail()->slug;
                $this->notificationService->unsubscribeFrom($slug, [$token]);
            }
        }
    }
}
