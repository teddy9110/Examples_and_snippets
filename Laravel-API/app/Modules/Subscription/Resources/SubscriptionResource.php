<?php

namespace Rhf\Modules\Subscription\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'reference_name' => $this->reference_name,
            'product_id' => $this->product_id,
            'duration' => $this->duration
        ];
    }
}
