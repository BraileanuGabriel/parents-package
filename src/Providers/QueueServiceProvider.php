<?php

namespace Parents\RequestPause\Providers;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        Queue::looping(function (Looping $event) {
            info("loop");
            return !Cache::has('pause_'.$event->queue.'_queue');
        });

        Queue::after(function (JobProcessed $job){
            Cache::forget('pause_'.$job->job->getQueue().'_queue');
        });
    }
}