<?php

namespace Rhf\Modules\System\Controllers;

use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\System\Resources\SettingResource;
use Rhf\Modules\System\Models\Setting;
use Rhf\Modules\System\Models\ActivityLog;

class SettingController extends Controller
{
    /**
     * Create a new ExerciseController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the available exercise categories.
     *
     * @param (int) categoryId
     * @param (int) contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $slug)
    {
        try {
            $setting = Setting::where('meta', '=', $slug)->first();
        } catch (\Exception $e) {
            throw new FitnessBadRequestException('Error: unable to retrieve settings. Please try again later.');
        }

        return response()->json(['status' => 'success', 'data' => new SettingResource($setting)]);
    }

    /**
     * Reset the facebook blocking flag to re-enable facebook.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblockFacebook(Request $request)
    {
        ActivityLog::where('action', '=', 'FacebookFail')->delete();
        return response()->json(['status' => 'success'], 200);
    }
}
