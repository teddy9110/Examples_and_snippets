<?php

namespace Rhf\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Notifications\Services\NotificationService;
use Rhf\Modules\User\Models\UserNotificationPreferences;

class SubTopics extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'topic_id',
        'active',
        'subscribe'
    ];

    protected $casts = [
        'active' => 'boolean',
        'subscribe' => 'boolean',
    ];

    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function topic()
    {
        return $this->belongsTo(Topics::class, 'topic_id', 'id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function createSubtopicAndSubscribeAll($data)
    {
        $subtopic = $this->createSubtopic($data);

        if ($data['active'] === 1 && $data['subscribe'] === 1) {
            $this->subscribeAllToTopic($subtopic);
        }
        return $subtopic;
    }

    public function updateSubtopicAndSubscribeAll($data)
    {
        $subtopic = $this->findOrFail($data['id']);
        //only unsubscribe if topic_id's are different
        if ($subtopic->topic_id !== $data['topic_id']) {
            //unsubscribe old topic slug
            $this->unsubscribeAllFromTopic($subtopic);
        }
        $subtopic->update($data);

        if ($data['active'] === 1 && $data['subscribe'] === 1) {
            $this->subscribeAllToTopic($subtopic);
        }
        return $subtopic;
    }

    public function remove($id)
    {
        $topic = $this->findOrFail($id);
        $this->unsubscribeAllFromTopic($topic);
        return $topic->delete();
    }

    /**
     * @param $data
     */
    private function createSubtopic($data)
    {
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->topic_id = $data['topic_id'];
        $this->slug = $data['slug'];
        $this->active = $data['active'];
        $this->subscribe = $data['subscribe'];
        $this->save();
        return $this;
    }

    /**
     * @param SubTopics $subtopic
     * @return mixed
     */
    public function subscribeAllToTopic(SubTopics $subtopic)
    {
        $users = UserNotificationPreferences::get();
        foreach ($users as $user) {
            $subtopics = $user->subtopics_ids;
            in_array($subtopic->id, $subtopics) ?: array_push($subtopics, $subtopic->id);
            $user->update(['subtopics_ids' => array_values($subtopics)]);
            $this->notificationService->subscribeTo($subtopic->slug, $user->device);
        }
        return $subtopics;
    }

    /**
     * @param $topic
     */
    public function unsubscribeAllFromTopic($topic): void
    {
        $users = UserNotificationPreferences::get();
        foreach ($users as $user) {
            $subtopics = $user->subtopics_ids;
            unset($subtopics[array_search($topic->id, $user->subtopics_ids)]);
            $user->update(['subtopics_ids' => array_values($subtopics)]);
            $this->notificationService->unsubscribeFrom($topic->slug, $user->device);
        }
    }
}
