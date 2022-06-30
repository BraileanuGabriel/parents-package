<?php

namespace Parents\RequestPause\Providers;

class ConfigServiceProvider extends BaseProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('job_pause.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'job_pause'
        );
    }
}