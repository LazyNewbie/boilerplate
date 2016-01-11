<?php

namespace LazyNewbie\Boilerplate;

use Illuminate\Support\ServiceProvider;

class BoilerplateServiceProvider extends ServiceProvider
{

    const CONFIG_NAME = "ln-boilerplate";


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
          __DIR__.'/assets' => base_path('resources/assets'),
          __DIR__.'/config' => config_path()
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // add widgets
        \View::addNamespace(
          \Config::get("ln-boilerplate.widgets.views_namespace"),
          \Config::get("ln-boilerplate.widgets.views_path")
        );
    }
}
