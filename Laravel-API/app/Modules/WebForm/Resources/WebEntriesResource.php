<?php

namespace Rhf\Modules\WebForm\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebEntriesResource extends JsonResource
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
            'entries' => WebEntryResource::collection($this->entries),
            'competition' => [
                'title' => $this->title,
                'slug' => $this->slug,
                'banner_image' => $this->getImage($this->desktop_image),
                'mobile_image' => $this->getImage($this->mobile_image),
                'entry_count' => $this->entries()->count()
            ]
        ];
    }
}
