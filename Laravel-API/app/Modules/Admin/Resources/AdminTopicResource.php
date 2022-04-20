<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->category,
            'slug' => $this->slug,
            'description' => $this->description,
            'subtopics' => empty($this->subtopics->items) ?
                $this->processSubtopics($this->subtopics) :
                []
        ];
    }

    public function processSubtopics($subtopics)
    {
        $topicsArray = [];
        foreach ($subtopics as $topic) {
            $topicsArray[] = [
                'id' => $topic['id'],
                'title' => $topic['title'],
                'description' => $topic['description'],
                'slug' => $topic['slug'],
                'active' => $topic['active'],
                'subscribe' => $topic['subscribe']
            ];
        }
        return $topicsArray;
    }
}
