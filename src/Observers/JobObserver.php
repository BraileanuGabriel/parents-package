<?php

namespace Parents\RequestPause\Observers;

use App\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver
{
    /**
     * @return void
     */
    public function deleted(Job $job)
    {
        Cache::forget('pause_'.$job->queue.'_queue');
    }
}