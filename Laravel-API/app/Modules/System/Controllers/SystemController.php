<?php

namespace Rhf\Modules\System\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Rhf\Http\Controllers\Controller;

class SystemController extends Controller
{
    /**
     * Health check for API
     *
     * @return Response
     */
    public function healthCheck()
    {
        return response([
            'status' => 'ok'
        ], 200);
    }

    /**
     * Terms and conditions view
     *
     * @return Application|Factory|View
     */
    public function tcs()
    {
        return view('information/tcs');
    }

    /**
     * Privacy policy view
     *
     * @return Application|Factory|View
     */
    public function privacyPolicy()
    {
        return view('information/privacypolicy');
    }
}
