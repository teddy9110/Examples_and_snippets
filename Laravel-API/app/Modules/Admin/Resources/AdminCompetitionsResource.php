<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminCompetitionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => json_decode($this->description, true),
            'desktop_image' => $this->getImage($this->desktop_image),
            'mobile_image' => $this->getImage($this->mobile_image),
            'app_image' => $this->getImage($this->app_image),
            'information' => json_decode($this->rules, true),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'active' => $this->active,
            'prize' => $this->prize,
            'status' => $this->closed ? 'Closed' : 'Open',
            'entries' => isset($this->entries) ? AdminEntryResource::collection($this->entries) : null,
            'winning_entry' => isset($this->winner) ? [
                'id' => $this->winner->entry->id,
                'title' => $this->winner->entry->title,
                'description' => substr($this->winner->entry->description, 0, 100),
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
