<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'message' => isset($this->attributes) ? $this->attributes->message : $this->message,
            'level_name' => isset($this->attributes) ? $this->attributes->attributes->level_name : $this->level_name,
            'date_time' => isset($this->attributes) ? $this->attributes->attributes->datetime : $this->datetime,
        ];
    }
}
