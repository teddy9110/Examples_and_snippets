<?php

namespace Rhf\Modules\WebForm\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Competition\Resources\EntryResource;
use Rhf\Modules\Competition\Resources\LeaderboardResource;

class WebCompetitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => json_decode($this->description),
            'banner_image' => $this->getImage($this->desktop_image),
            'mobile_image' => $this->getImage($this->mobile_image),
            'rules' => json_decode($this->rules),
            'prize' => $this->prize,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'closed' => $this->closed,
            'leaderboard' => LeaderboardResource::collection($this->leaderboard),
            'entries' => EntryResource::collection($this->entries),
            'winner' => is_null($this->winner) ? null : new EntryResource($this->winner->entry),
        ];
    }
}
