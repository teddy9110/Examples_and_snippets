<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserGraphResource extends JsonResource
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
            'value' => floatval($this->value),
            'date' => $this->date->format('d-m-Y'),
            'note' => $this->notes->note ?? null,
            'period' => $this->notes->period ?? null
        ];
    }
}
