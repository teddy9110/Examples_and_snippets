<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminTransformationStoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'dob' => $this->date_of_birth,
            'weight' => [
                'loss' => $this->weight_loss,
                'current' => $this->current_weight,
                'starting' => $this->start_weight,
            ],
            'story' => $this->story,
            'marketing_accepted' => $this->marketing_accepted,
            'remain_anonymous' => $this->remain_anonymous,
            'before_photo' => $this->getTransformationImage($this->before_photo),
            'after_photo' => $this->getTransformationImage($this->after_photo),
            'submitted_date' => date('d-m-Y', strtotime($this->created_at))
        ];
    }
}
