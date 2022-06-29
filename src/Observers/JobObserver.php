<?php

namespace func\HelloWorld\Observers;

use App\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver
{
    /**
     * Handle the quiz "deleted" event.
     *
     * @param  \App\Quiz  $quiz
     * @return void
     */
    public function deleted(Job $job)
    {
        info('here');
        Cache::forget('pause_'.$job->queue.'_queue');
    }
}