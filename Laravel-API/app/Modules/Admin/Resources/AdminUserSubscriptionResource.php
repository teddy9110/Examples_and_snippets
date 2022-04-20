<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AdminUserSubscriptionResource extends JsonResource
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
            'email' => $this->email,
            'subscription_provider' => $this->subscription_provider,
            'subscription_plan' => $this->subscription_plan,
            'subscription_frequency' => $this->subscription_frequency,
            'purchase_date' => $this->purchase_date,
            'expiry_date' => Carbon::parse($this->expiry_date)->toDateTimeString(),
            'subscription_reference' => $this->subscription_reference,
        ];
    }
}
