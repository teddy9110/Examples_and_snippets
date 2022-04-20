<?php

namespace Rhf\Modules\Development\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Activity\Models\Activity;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'staff_user' => $this->staff_user,
            'has_paid' => $this->paid == 1,
            'expire_at' => !is_null($this->expiry_date) ? $this->expiry_date->format('Y-m-d') : null,
        ];
    }
}
