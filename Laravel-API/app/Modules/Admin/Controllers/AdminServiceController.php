<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminServiceRequest;
use Rhf\Modules\System\Models\Service;
use Rhf\Modules\System\Resources\ServiceResource;

class AdminServiceController extends Controller
{
    /**
     * Get services
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return ServiceResource::collection(Service::all());
    }

    /**
     * Get service by slug
     *
     * @param string $slug
     *
     * @return ServiceResource
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        return new ServiceResource($service);
    }

    /**
     * Update service by slug
     *
     * @param string $slug
     * @param AdminServiceRequest $request
     *
     * @return ServiceResource
     */
    public function updateService(string $slug, AdminServiceRequest $request)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        $service->status = $request->status;
        $service->save();

        return new ServiceResource($service);
    }
}
