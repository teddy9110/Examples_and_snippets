<?php

namespace Rhf\Modules\Notifications\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationPreferencesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $notifications = [];
        foreach ($this->resource as $resource) {
            $notifications[] = [
                'title' => $resource['title'],
                'description' => $resource['description'],
                'topics' => array_key_exists('topics', $resource) == true ?
                    $this->processTopics($resource['topics']) :
                    []
            ];
        }
        return ['notifications' => $notifications];
    }

    public function processTopics($topics)
    {
        $topicsArray = [];
        foreach ($topics as $topic) {
            $topicsArray[] = [
                'title' => ucfirst($topic['title']),
                'description' => $topic['description'],
                'slug' => $topic['slug'],
                'enabled' => $topic['enabled'],
            ];
        }
        return $topicsArray;
    }
}
