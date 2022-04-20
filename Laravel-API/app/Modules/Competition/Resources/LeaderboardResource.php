<?php

namespace Rhf\Modules\Competition\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
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
            'submitted_by' => [
                'forename' => $this->user->first_name,
                'surname' => $this->user->surname,
            ],
            'competition' => [
                'id' => $this->competition->id,
                'title' => $this->competition->title,
                'closed' => $this->competition->closed,
            ],
            'position' => $this->position,
            'tied' => $this->tied,
        ];
    }
}
