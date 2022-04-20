<?php

namespace Rhf\Modules\System\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\System\Models\AppVersion;
use Rhf\Modules\System\Requests\AppLatestRequest;
use Rhf\Modules\System\Requests\AppVersionRequest;
use Rhf\Modules\System\Resources\AppLatestResource;
use Rhf\Modules\System\Resources\AppVersionResource;

class AppController extends Controller
{
    /**
     * Get the app version info for specific platform
     *
     * @param string $platform
     * @param AppVersionRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function showVersion(string $platform, AppVersionRequest $request)
    {
        $appVersions = AppVersion::where('platform', $platform)->get();
        return AppVersionResource::collection($appVersions);
    }

    /**
     * Get the latest app info for specific platform and date
     *
     * @param string $platform
     * @param string $date
     * @param AppLatestRequest $request
     *
     * @return AppLatestResource
     */
    public function getStatus(AppLatestRequest $request, string $platform, string $date)
    {
        return new AppLatestResource([
            'platform' => $platform,
            'date' => is_null($date) ? now() : Carbon::parse($date),
        ]);
    }
}
