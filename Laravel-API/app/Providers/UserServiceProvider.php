<?php

namespace Rhf\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Rhf\Modules\Admin\Services\AdminUserService;
use Rhf\Modules\User\Services\TargetService;
use Rhf\Modules\User\Services\UserFileService;
use Rhf\Modules\User\Services\UserService;

class UserServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    public function register()
    {
        $this->app->bind(UserFileService::class, function (Container $app) {
            return new UserFileService();
        });

        $this->app->bind(AdminUserService::class, function (Container $app) {
            return new AdminUserService($app->make(TargetService::class), $app->make(UserService::class));
        });
    }
}
