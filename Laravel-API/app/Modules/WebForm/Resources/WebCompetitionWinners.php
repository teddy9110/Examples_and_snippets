<?php

namespace Rhf\Modules\WebForm\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebCompetitionWinners extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return isset($this->winner) ? $this->winningEntries($this) : [];
    }

    public function winningEntries($entry)
    {
        return [
            'id' => $entry->winner->entry->id,
            'image_url' => $entry->getImage($this->winner->entry->image),
            'description' => $entry->winner->entry->description,
            'vote_count' => $entry->winner->entry->votes,
            'share_url' => $entry->winner->entry->url,
            'submitted_by' => [
                'forename' => $entry->winner->user->first_name,
                'surname' => $entry->winner->user->surname
            ],
            'competition' => [
                'title' => $entry->title,
                'slug' => $entry->slug,
                'closed' => $entry->closed,
            ]
        ];
    }
}
