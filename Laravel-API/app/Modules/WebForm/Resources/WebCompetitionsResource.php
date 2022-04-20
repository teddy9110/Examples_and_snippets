<?php

namespace Rhf\Modules\WebForm\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Competition\Resources\EntryResource;
use Rhf\Modules\Competition\Resources\LeaderboardResource;

class WebCompetitionsResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'banner_image' => $this->getImage($this->desktop_image),
            'mobile_image' => $this->getImage($this->mobile_image),
            'closed' => $this->closed,
        ];
    }
}
