<?php

namespace Rhf\Http\Middleware;

use Closure;
use Sentry\State\Scope;

use function Sentry\configureScope;

class SentryUser
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
        if (auth('api')->check() && app()->bound('sentry')) {
            configureScope(function (Scope $scope): void {
                $scope->setUser([
                    'id' => auth('api')->user()->id,
                    'email' => auth('api')->user()->email,
                ]);
            });
        }

        return $next($request);
    }
}
