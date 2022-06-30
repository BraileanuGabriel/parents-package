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
        info('======= '.$attempts.' ======');
    }

    public function findDelay($attempts){
        $config = config('job_pause.pause_job_delay');
        return $config[$attempts] ?? $config[6];
    }

    public function pause($queue, $delay){
        Cache::tags(['pause_keys'])->put('pause_'.$queue.'_queue', $delay, $delay);
        info($delay);
        info($queue);
        info(Cache::get('pause_'.$queue.'_queue'));
        info('================');
    }
}
