<?php

namespace Rhf\Modules\System\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResource extends JsonResource
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
            'type' => $this->type,
            'version' => $this->version,
            'build_number' => $this->build_number,
        ];
    }
}
