<?php

namespace Rhf\Modules\Competition\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'competition' => [
                'id' => $this->competition->id,
                'title' => $this->competition->title,
                'start_date' => iso_date($this->competition->start_date),
                'end_date' => iso_date($this->competition->end_date),
                'status' => $this->active ? 'Active' : ($this->closed ? 'Closed' : 'Open'),
            ],
            'submitted_by' => [
                'name' => $this->user->name,
                'forename' => $this->user->first_name,
                'surname' => $this->user->surname,
                'email' => $this->user->email,
            ],
            'created_at' => iso_date($this->created_at),
            'date_created' => Carbon::parse($this->created_at)->diffForHumans(),
            'suspended' => $this->suspended ?? false
        ];
    }
}
