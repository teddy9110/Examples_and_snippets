<?php

namespace Rhf\Modules\Notifications\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiNotificationsResource extends JsonResource
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
            'content' => stripslashes($this->content),
            'action_text' => $this->action_text,
            'type' => $this->type,
        ];
    }
}
