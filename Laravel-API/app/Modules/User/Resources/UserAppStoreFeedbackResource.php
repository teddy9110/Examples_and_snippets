<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\User\Resources\UserAppStoreFeedbackTopicsResource;

class UserAppStoreFeedbackResource extends JsonResource
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
            'score' => $this->score,
            'comments' => $this->comments,
            'feedback_topics' => UserAppStoreFeedbackTopicsResource::collection($this->topics),
        ];
    }
}
