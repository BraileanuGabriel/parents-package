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
        switch ($attempts){
            case 1: return $config[1];
            case 2: return $config[2];
            case 3: return $config[3];
            case 4: return $config[4];
            case 5: return $config[5];
            case $attempts>5: return $config[6];
        }
    }

    public function pause($queue, $delay){
        Cache::put('pause_'.$queue.'_queue', $delay, $delay);
    }
}
