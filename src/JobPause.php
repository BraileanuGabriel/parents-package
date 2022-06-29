<?php

namespace Parents\RequestPause;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JobPause
{
    public function check($thisJob, $queue = 'default'){
        if($thisJob->attempts() == 0) return;

        if($thisJob->attempts() != 0 && !cache()->get('pause_'.$queue.'_queue')){
            info('here');
            return;
        }

        if($attempts = DB::table('jobs')->where('queue', $queue)->sum('attempts')){
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
