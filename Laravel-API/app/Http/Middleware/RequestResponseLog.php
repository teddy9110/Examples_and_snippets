<?php

namespace Rhf\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class RequestResponseLog
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $requestData = $request->all();

        // Check if we have a user
        try {
            if (auth('api')->check()) {
                $requestData['user_id'] = auth('api')->user()->id;
            }
        } catch (TokenBlacklistedException $e) {
            // ignore blacklisted token, continue with log
        }

        // Get the request URL
        $requestData['url_with_query'] = ($request->getPathInfo()
            . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

        // Log requests and responses for any errors
        if ($response->status() > 399 && $response->status() != 401) {
            Log::info('app.request_errors', ['request' => $requestData]);
            Log::info('app.response_errors', ['response' => $response]);
        }

        return $response;
    }
}
