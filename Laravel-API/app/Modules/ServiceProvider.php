<?php

namespace Rhf\Modules;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $modules = config("module.modules");
        foreach ($modules as $module) {
            if (file_exists(__DIR__ . '/' . $module . '/routes.php')) {
                include __DIR__ . '/' . $module . '/routes.php';
            }
            if (is_dir(__DIR__ . '/' . $module . '/Views')) {
                $this->loadViewsFrom(__DIR__ . '/' . $module . '/Views', $module);
            }
        }
    }

    public function register()
    {
    }
}
