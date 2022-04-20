<?php

namespace Rhf\Modules\Competition\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionEntryResource extends JsonResource
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
            'description' => $this->description,
            'image_url' => $this->getImage($this->image),
            'vote_count' => $this->votes,
            'share_url' => $this->url,
            'competition' => isset($this->competition) ? [
                'title' => $this->competition->title,
                'start_date' => iso_date($this->competition->start_date),
                'end_date' => iso_date($this->competition->end_date),
                'status' => ($this->active ? 'Active' : $this->closed) ? 'Closed' : 'Open',
            ] : null,
            'submitted_by' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'suspended' => $this->suspended ?? false,
            'voted' => $this->voted($this->id),
            'reported_by_user' => $this->reported($this->id),
            'created_at' => iso_date($this->created_at),
            'date_created' => Carbon::parse($this->created_at)->diffForHumans(),
            'position' => $this->position,
            'tied' => $this->tied,
        ];
    }
}
