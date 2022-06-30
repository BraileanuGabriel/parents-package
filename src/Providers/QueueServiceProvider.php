<?php

namespace Parents\RequestPause\Providers;

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Parents\RequestPause\ServiceProvider\BaseProvider;

class QueueServiceProvider extends BaseProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        Queue::looping(function (Looping $event) {
            return !Cache::has('pause_'.$event->queue.'_queue');
        });

        Queue::after(function (JobProcessed $job){
            Cache::forget('pause_'.$job->job->getQueue().'_queue');
        });
    }
}