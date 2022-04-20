<?php

namespace Rhf\Modules\System\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\System\Models\AppVersion;
use Rhf\Modules\System\Models\Service;

class AppLatestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        $platform = $this['platform'];
        $date = $this['date'];

        $appVersions = AppVersion::where('platform', $platform)->get();
        $newRecipeCount = Recipe::where('created_at', '>=', $date)->count();
        $services = Service::all();

        return [
            'versions' => AppVersionResource::collection($appVersions),
            'services' => ServiceResource::collection($services),
            'indicators' => [
                'dashboard' => 0,
                'recipe_book' => $newRecipeCount,
                'life_plan' => 0,
                'store' => 0,
            ],
        ];
    }
}
