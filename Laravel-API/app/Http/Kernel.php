<?php

namespace Rhf\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Rhf\Http\Middleware\DebugMiddleware;
use Rhf\Http\Middleware\Feature;
use Rhf\Http\Middleware\LastActive;
use Rhf\Http\Middleware\SentryUser;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * This middleware is run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Rhf\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Rhf\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Rhf\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            SentryUser::class,
            LastActive::class,
        ],

        'api' => [
            'throttle:1000,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            DebugMiddleware::class,
            SentryUser::class,
            LastActive::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * This middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Rhf\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Rhf\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => \Rhf\Http\Middleware\Admin::class,
        'api_key' => \Rhf\Http\Middleware\ApiToken::class,
        'facebook' => \Rhf\Http\Middleware\Facebook::class,
        'feature' => Feature::class
    ];
}
