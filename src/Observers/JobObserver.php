<?php

namespace Parents\RequestPause\Observers;

use App\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver
{
    /**
     * Handle the quiz "deleted" event.
     * @return void
     */
    public function deleted(Job $job)
    {
//        Cache::forget('pause_'.$job->queue.'_queue');
        Cache::tags('pause_keys')->flush();
    }
}