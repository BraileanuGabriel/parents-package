<?php

namespace Parents\RequestPause;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JobPause
{
    public function check($queue = 'default'){
        if($attempts = DB::table('jobs')->where('queue', $queue)->sum('attempts')){
            $delay = $this->findDelay($attempts);
            $this->pause($queue, $delay);
        }
    }

    public function findDelay($attempts){
        $config = config('job_pause.pause_job_delay');
        return $config[$attempts] ?? end($config);
    }

    public function pause($queue, $delay){
        Cache::put('pause_'.$queue.'_queue', $delay, $delay);
    }
}
