<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
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
            'name' => "$this->first_name $this->surname",
            'email' => $this->email,
            'staff_user' => $this->staff_user,
            'has_paid' => $this->paid == 1,
            'has_mfp' => $this->hasConnectedMfp(),
            'created_at' => !is_null($this->created_at) ? $this->created_at->format('d/m/Y') : null,
            'deleted_at' => !is_null($this->deleted_at) ? $this->deleted_at->format('d/m/Y') : null,
            'expire_at' => !is_null($this->expiry_date) ? $this->expiry_date->format('d/m/Y') : null,
        ];
    }
}
