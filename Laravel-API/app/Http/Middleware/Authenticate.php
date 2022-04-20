<?php

namespace Rhf\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as LaravelAuth;
use Rhf\Exceptions\FitnessHttpException;

class Authenticate extends LaravelAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // Check active & paid flags, and expiry date
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = Auth::user();
        if ($user->isActive() && $user->isPaid()) {
            return $next($request);
        }

        throw new FitnessHttpException('Unauthenticated (Inactive/Expired).', 403);
    }
}
