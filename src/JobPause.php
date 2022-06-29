<?php

namespace func\HelloWorld;

use App\Job;
use Illuminate\Support\Facades\Cache;

class JobPause
{
    public function check($queue = 'default'){
        $attempts = Job::where('queue', $queue)->sum('attempts');
        if($attempts){
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
