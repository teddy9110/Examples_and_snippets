<?php

namespace Rhf\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Rhf\Modules\Content\Services\ContentService;
use Rhf\Modules\Content\Services\ContentVideoFileService;

class ContentServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    public function register()
    {
        $this->app->bind(ContentService::class, function ($app) {
            return new ContentService();
        });

        $this->app->bind(ContentVideoFileService::class, function (Container $app) {
            return new ContentVideoFileService();
        });
    }
}
