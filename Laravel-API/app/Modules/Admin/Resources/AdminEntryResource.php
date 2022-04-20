<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminEntryResource extends JsonResource
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
            'description' => $this->description,
            'image_url' => $this->getImage($this->image),
            'vote_count' => $this->votes,
            'share_url' => $this->url,
            'reports' => $this->reports,
            'report_details' => $this->reportDetails,
            'competition_id' => $this->competition_id,
            'submitted_by' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'suspended' => $this->suspended ?? false,
        ];
    }
}
