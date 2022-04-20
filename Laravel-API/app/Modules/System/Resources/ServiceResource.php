<?php

namespace Rhf\Modules\System\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'slug' => $this->slug,
            'status' => $this->status,
            'name' => $this->name
        ];
    }
}
