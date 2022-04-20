<?php

namespace Rhf\Modules\MyFitnessPal\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\MyFitnessPal\Services\MyFitnessPalService;
use Rhf\Modules\MyFitnessPal\Services\DiaryService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Services\UserService;
use Rhf\Modules\System\Models\ActivityLog;
use Carbon\Carbon;
use Rhf\Modules\System\Models\Service;

class MyFitnessPalController extends Controller
{
    /**
     * Authenticate the user with My Fitness Pal.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        $service = Service::where('slug', '=', 'mfp')->first();
        $url = config('app.url');

        if ($service->status == 'down') {
            return response([
                'status' => 'success',
                'data' => "$url/status/myfitnesspal.com"
            ], 200);
        }

        // Check if we are already auth'd
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();
        if ($user->hasConnectedMfp()) {
            throw new FitnessBadRequestException('Error: User has already authenticated with MyFitnessPal.');
        }

        try {
            $myFitnessPalService = new MyFitnessPalService();
            $authUrl = $myFitnessPalService->authUrl();
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: unable to authenticate with MyFitnessPal. Please try again later.'
            );
        }

        return response()->json(['status' => 'success', 'data' => $authUrl]);
    }

    /**
     * Handle response url redirect from My Fitness Pal auth.
     *
     */
    public function authComplete(Request $request)
    {
        if (!$request->has('code') || !$request->has('state')) {
            throw new FitnessBadRequestException(
                'Error: unable to authenticate with MyFitnessPal. Please try again later.'
            );
        }

        // Get the user by state and set their auth code
        try {
            $user = User::where('mfp_state_token', '=', $request->get('state'))->firstOrFail();
            $userService = new UserService();
            $userService->setUser($user);
            $userService->updatePreference('mfp_authentication_code', $request->get('code'));
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: unable to connect and sync with MyFitnessPal. Please try again later.'
            );
        }

        // Get the token and refresh token
        try {
            auth('api')->setUser($user);
            $myFitnessPalService = new MyFitnessPalService();
            $myFitnessPalService->getFirstToken();
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: unable to authenticate with MyFitnessPal. Please try again later.'
            );
        }

        // log MFP link
        try {
            $log = new ActivityLog();
            $log->user_id = $user->id;
            $log->action = 'MyFitnessPalLinked';
            $log->save();
        } catch (\Exception $e) {
            // should never throw, silently fail log rather than throw exception
        }

        return view('message', ['message' => 'Success, your MyFitnessPal account has now been linked.']);
    }

    /**
     * Return if the user is fully authenticated with My Fitness Pal.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        try {
            // Check if we are already auth'd
            /** @var \Rhf\Modules\User\Models\User $user */
            $user = auth('api')->user();
            $status = $user->hasConnectedMfp();
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: unable to verify your connectivity to MyFitnessPal. Please contact Team RH Support'
            );
        }

        return response()->json(['status' => 'success', 'data' => ['authenticated_with_mfp' => $status]]);
    }

    /**
     * Sync data from MyFitnessPal for the given day.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request, $date)
    {
        try {
            $date = Carbon::parse($date);
            DiaryService::sync($date);
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Error: unable to sync your MyFitnessPal. Please contact Team RH Support'
            );
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Test the MyFitnessPal Connection.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        $myFitnessPalService = new MyFitnessPalService();

        return response()
            ->json(['status' => 'success', 'data' => ['authentication_url' => $myFitnessPalService->authUrl()]]);
    }

    /**
     * Unlink MyFitnessPal from the users account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlink(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth('api')->user();

        // Ensure the user is connected to MFP before proceeding
        if (!$user->hasConnectedMfp()) {
            throw new FitnessPreconditionException('Error: User has not linked MyFitnessPal.');
        }

        $success = $user->removeMfp();

        if ($success) {
            return response()->json([
                'status' => 'success',
                'data' => ['authenticated_with_mfp' => false]
            ], 200);
        }

        throw new FitnessPreconditionException('Error: User has not linked MyFitnessPal.');
    }

    /**
     * MyFitnessPal down page
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function mfpStatus(Request $request)
    {
        return view('mfpstatus');
    }
}
