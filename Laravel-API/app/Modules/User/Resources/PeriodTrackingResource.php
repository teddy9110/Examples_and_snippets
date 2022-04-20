<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeriodTrackingResource extends JsonResource
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
            'period_tracker' => $this->period_tracker
        ];
    }
}
