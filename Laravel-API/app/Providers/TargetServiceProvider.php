<?php

namespace Rhf\Providers;

use Illuminate\Support\ServiceProvider;
use Rhf\Modules\User\Services\TargetService;

class TargetServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    public function register()
    {
        $this->app->bind(TargetService::class, function ($app) {
            return new TargetService();
        });
    }
}
