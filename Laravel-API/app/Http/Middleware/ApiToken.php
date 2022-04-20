<?php

namespace Rhf\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;
use Rhf\Modules\Auth\Models\ApiKey;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check and retrieve token
        if ($request->header('authorization')) {
            $api_key = explode('Bearer ', $request->header('authorization'))[1];
        }

        // Get all the keys
        $keys = ApiKey::get();

        // Check the key
        foreach ($keys as $key) {
            if (Hash::check($api_key, $key->api_key)) {
                return $next($request);
            }
        }

        return response()->json('Unauthorized', 401);
    }
}
