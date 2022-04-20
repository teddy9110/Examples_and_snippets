<?php

namespace Rhf\Modules\Competition\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'slug' => $this->slug,
            'information' => json_decode($this->description, true),
            'image' => $this->getImage($this->app_image),
            'rules' => json_decode($this->rules, true),
            'prize' => $this->prize,
            'start_date' => iso_date($this->start_date),
            'end_date' => iso_date($this->end_date),
        ];
    }
}
