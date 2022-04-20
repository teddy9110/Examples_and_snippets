<?php

namespace Rhf\Modules\System\Controllers;

use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\System\Models\Setting;

class ConfigController extends Controller
{
    public function config(Request $request)
    {
        return response()->json([
            'data' => Setting::all()->reduce(function ($carry, $item) {
                $carry[$item->meta] = $item->json ? json_decode($item->value, true) : $item->value;
                return $carry;
            }, []),
        ]);
    }
}
