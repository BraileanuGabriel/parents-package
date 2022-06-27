<?php

namespace func\HelloWorld;

use Illuminate\Support\ServiceProvider;

namespace func\HelloWorld;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('awesome.php'),
        ], 'awesome_config');
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'awesome'
        );

    }
}