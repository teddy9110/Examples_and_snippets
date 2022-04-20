<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\User\Services\UserFileService;

class UserProgressPictureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        /** @var UserFileService $userFileService */
        $userFileService = app(UserFileService::class);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type,
            'url' => $userFileService->getPublicUrl($this),
            'visibility' => $this->public ? 'public' : 'private',
        ];
    }
}
