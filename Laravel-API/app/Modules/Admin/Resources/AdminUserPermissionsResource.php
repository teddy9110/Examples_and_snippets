<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserPermissionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $permissions = [];

        foreach ($this->permissions as $permission => $allowed) {
            if ($allowed) {
                array_push($permissions, $permission);
            }
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'permissions' => $permissions
        ];
    }
}
