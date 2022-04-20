<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\User\Models\User;

class AdminStaffNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $loggedBy = is_null($this->logged_by) ? null : (User::find($this->logged_by)->name ?? null);
        $updatedBy = is_null($this->last_updated_by) ? null : (User::find($this->last_updated_by)->name ?? null);

        return [
            'id' => $this->id,
            'note' => $this->note,
            'logged_by' => $loggedBy,
            'last_updated_by' => $updatedBy,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
