<?php

namespace Rhf\Modules\Admin\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminFeatureRequest;
use Rhf\Modules\Admin\Resources\AdminFeatureResponse;
use Rhf\Modules\System\Models\Feature;
use Rhf\Modules\System\Models\Setting;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminFeatureController extends Controller
{
    public function toggleFeature(AdminFeatureRequest $request, $slug)
    {
        $setting = Setting::where('meta', 'features')->first();
        if (is_null($setting) || !isset($setting->value)) {
            throw new NotFoundHttpException();
        }

        $features = json_decode($setting->value, true);
        if (!isset($features[$slug])) {
            throw new NotFoundHttpException();
        }

        $features[$slug] = $request->json('value');
        $setting->update(['value' => json_encode($features)]);

        return response()->json(['status' => 'success']);
    }

    public function features()
    {
        return AdminFeatureResponse::collection(Feature::all());
    }

    public function toggle($id)
    {
        $feature = Feature::where('id', $id)->first();
        $feature->update([
            'active' => !$feature->active
        ]);
        return response('success', 200);
    }
}
