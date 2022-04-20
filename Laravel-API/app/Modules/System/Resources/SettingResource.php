<?php

namespace Rhf\Modules\System\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Content\Resources\BasicContentResource;
use Rhf\Modules\Content\Resources\ContentResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $return = [
            'value' => $this->value,
        ];

        return $return;
    }
}
