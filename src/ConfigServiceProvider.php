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
            __DIR__.'/config/config.php' => config_path('job_pause.php'),
        ]);
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'job_pause'
        );

    }
}