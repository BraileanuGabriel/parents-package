<?php

namespace EBS\Handlers\Providers;

use Illuminate\Queue\Events\JobFailed;
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
            return !Cache::has('pause_'.$event->queue.'_queue');
        });

        Queue::after(function (JobProcessed $job){
            Cache::forget('pause_'.$job->job->getQueue().'_queue');
        });

        Queue::failing(function (JobFailed $job){
            Cache::forget('pause_'.$job->job->getQueue().'_queue');
        });
    }
}