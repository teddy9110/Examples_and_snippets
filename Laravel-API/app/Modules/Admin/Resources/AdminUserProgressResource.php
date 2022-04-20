<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Rhf\Modules\User\Services\UserFileService;

class AdminUserProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $userFileService = app(UserFileService::class);

        $pictures = $this
            ->progressPicture()
            ->orderByRaw('FIELD(type, "front", "side")')
            ->get();

        $picturesData = [];

        foreach ($pictures as $picture) {
            $picturesData[] = [
                'id' => $picture->id,
                'type' => $picture->type,
                'uri' => $userFileService->getPublicUrl($picture),
                'download_uri' => URL::temporarySignedRoute(
                    'download-progress-picture',
                    now()->addMinutes(30),
                    ['id' => $picture->id]
                )
            ];
        }

        return [
            'id' => $this->id,
            'weight' => floatval($this->weight_value),
            'date' => $this->updated_at->format('d/m/Y'),
            'pictures' => $picturesData,
        ];
    }
}
