<?php

namespace Parents\RequestPause\Providers;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '../config/config.php' => config_path('job_pause.php'),
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
            __DIR__ . '../config/config.php', 'job_pause'
        );

    }
}