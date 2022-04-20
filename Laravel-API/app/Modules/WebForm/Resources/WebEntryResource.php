<?php

namespace Rhf\Modules\WebForm\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class WebEntryResource extends JsonResource
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
            'slug' => Str::slug($this->title),
            'description' => $this->description,
            'image_url' => $this->getImage($this->image),
            'vote_count' => $this->votes,
            'share_url' => $this->url,
            'created_at' => iso_date($this->created_at),
            'date_created' => Carbon::parse($this->created_at)->diffForHumans(),
            'position' => $this->position,
            'tied' => $this->tied,
            'submitted_by' => [
                'forename' => $this->user->first_name,
                'surname' => $this->user->surname
            ],
            'competition' => [
                'title' => $this->competition->title,
                'slug' => $this->competition->slug,
                'banner_image' => $this->getImage($this->competition->desktop_image),
                'mobile_image' => $this->getImage($this->competition->mobile_image),
                'closed' => $this->competition->closed,
            ]
        ];
    }
}
