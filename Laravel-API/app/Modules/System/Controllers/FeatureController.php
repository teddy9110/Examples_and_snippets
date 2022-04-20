<?php

namespace Rhf\Modules\System\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\System\Models\Feature;
use Rhf\Modules\System\Resources\FeatureResource;

class FeatureController extends Controller
{
    public function index()
    {
        if (api_version() >= 20220228) {
            return FeatureResource::collection(Feature::all());
        }
        return FeatureResource::collection(Feature::where('slug', 'workouts_v3')->get());
    }

    public function featureBySlug($slug)
    {
        $feature = Feature::where('slug', $slug)->firstOrFail();
        return new FeatureResource($feature);
    }
}
