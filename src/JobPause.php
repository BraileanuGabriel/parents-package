<?php

namespace Parents\RequestPause;

use App\Job;
use Illuminate\Support\Facades\Cache;

class JobPause
{
    public function check($thisJob, $queue = 'default'){
        if($thisJob->attempts() == 0) return;
        info("here");
        $attempts = Job::where('queue', $queue)->sum('attempts');
        info(Job::all());
        if($attempts){
            info($attempts);
            $delay = $this->findDelay($attempts);
            $this->pause($queue, $delay);
        }
    }

    public function findDelay($attempts){
        $config = config('job_pause.pause_job_delay');
        return $config[$attempts] ?? $config[6];
    }

    public function pause($queue, $delay){
        Cache::put('pause_'.$queue.'_queue', $delay, $delay);
    }
}
