<?php

namespace Rhf\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Feature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $feature)
    {
        $isActive = feature_enabled($feature);
        if (!$isActive) {
            throw new NotFoundHttpException();
        }
        return $next($request);
    }
}
