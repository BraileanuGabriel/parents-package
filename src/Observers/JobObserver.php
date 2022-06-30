<?php

namespace Parents\RequestPause\Observers;

use Illuminate\Support\Facades\Cache;

class JobObserver
{
    /**
     * @return void
     */
    public function deleted()
    {
        Cache::tags(['pause_keys'])->flush();
    }
}