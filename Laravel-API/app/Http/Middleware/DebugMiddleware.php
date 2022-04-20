<?php

namespace Rhf\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

/**
 * Class DebugMiddleware
 *
 * @package \Rhf\Http\Middleware
 */
class DebugMiddleware
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
        $response = $next($request);

        if (
            $response instanceof JsonResponse &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled() &&
            is_object($response->getData())
        ) {
            $response->setData($response->getData(true) + [
                    '_debugbar' => app('debugbar')->getData(),
                ]);
        }

        return $response;
    }
}
