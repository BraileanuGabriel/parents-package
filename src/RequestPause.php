<?php

namespace func\HelloWorld;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class RequestPause
{
    public function createHandlerStack($tries = 5)
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry(function () use($tries) {return $tries;}, $this->retryDelay()));
    }

    protected function retryDelay()
    {
        $config = config('job_pause.pause_job_delay');
        return function ($numberOfRetries) use($config) {
            info($numberOfRetries.', '.$config[$numberOfRetries]);
            return $config[$numberOfRetries]*1000 ?? $config[6]*1000;
        };
    }
}