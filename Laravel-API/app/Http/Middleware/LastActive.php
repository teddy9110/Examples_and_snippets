<?php

namespace Rhf\Http\Middleware;

use Closure;

class LastActive
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
        if (!auth('api')->check()) {
            return $next($request);
        }
        $user = auth('api')->user();
        $user->timestamps = false;

        if (is_null($user->last_active) || $user->last_active->diffInHours(now()) !== 0) {
            $user->update([
                'last_active' => now()
            ]);
        }

        return $next($request);
    }
}
