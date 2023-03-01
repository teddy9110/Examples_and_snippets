<?php

namespace App\Http\Controllers\Api;

use App\Facades\ExternalLog;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalLogResource;
use App\Models\Tapestry\ServiceLog;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExternalLogController extends Controller
{
    /**
     * Find a Service Log from loki
     *
     * @param ServiceLog $serviceLog
     *
     * @return AnonymousResourceCollection
     */
    public function index(ServiceLog $serviceLog): AnonymousResourceCollection
    {
        try {
            $integration = $serviceLog->integration;
            if (mb_stripos($integration->server, '-k8s') === false) {
                $driver = 'DataDog';
            }

            $externalLogService = ExternalLog::driver($driver ?? null);
            $logs = $externalLogService->getServiceLogs($serviceLog);

            return ExternalLogpResource::collection($logs);
        } catch (Exception $exception) {
            abort(500, $exception);
        }
    }
}
