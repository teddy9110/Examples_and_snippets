<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAppStoreReviewResource extends JsonResource
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
            'present_review_dialog' => $this->present_review_dialog,
            'next_review_request' => iso_date($this->next_review_request),
            'last_review_submitted' => iso_date($this->last_review_submitted),
            'user_response' => $this->user_response,
        ];
    }
}
