<?php

namespace Rhf\Modules\WebForm\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Competition\Resources\EntryResource;
use Rhf\Modules\Competition\Resources\LeaderboardResource;

class WebCompetitionEntriesResource extends JsonResource
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
            'description' => $this->description,
            'image_url' => $this->getImage($this->image),
            'vote_count' => $this->votes,
            'share_url' => $this->url,
            'created_at' => $this->convertDate($this->created_at),
            'date_created' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
