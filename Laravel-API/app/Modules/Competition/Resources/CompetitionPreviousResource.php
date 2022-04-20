<?php

namespace Rhf\Modules\Competition\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionPreviousResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'start_date' => iso_date($this->start_date),
            'end_date' => iso_date($this->end_date),
            'winning_entry' => isset($this->winner) ? [
                'id' => $this->winner->entry->id,
                'title' => $this->winner->entry->title,
                'description' => $this->winner->entry->description,
                'image_url' => $this->getImage($this->winner->entry->image),
                'vote_count' => $this->winner->entry->votes,
                'share_url' => $this->winner->entry->url,
                'submitted_by' => [
                    'name' => $this->winner->user->name,
                    'email' => $this->winner->user->email,
                ]
            ] : null
        ];
    }
}
