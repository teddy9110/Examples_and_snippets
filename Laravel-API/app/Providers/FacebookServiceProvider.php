<?php

namespace Rhf\Providers;

use Rhf\Modules\System\Services\FacebookExtender;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FacebookExtender::class, function ($app) {
            $config = config('services.facebook');
            return new FacebookExtender([
                'app_id' => $config['client_id'],
                'app_secret' => $config['client_secret'],
                'default_graph_version' => 'v3.1',
            ]);
        });
    }
}
