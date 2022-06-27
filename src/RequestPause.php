<?php

namespace func\HelloWorld;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

abstract class RequestPause
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'handler' => $this->createHandlerStack(),
            'headers' => [
                'Accept' => 'application/json',
            ],
            'verify' => !config('services.w_parents.env')
        ]);
    }

    public function createHandlerStack($tries = 5): HandlerStack
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry(function () use($tries) {return $tries;}, $this->retryDelay()));
        return $stack;
    }

    /**
     * @return callable
     */
    protected function retryDelay()
    {
        $config = config('job_pause.pause_job_delay');
        return function ($numberOfRetries) use($config) {
            info($numberOfRetries.', '.$config[$numberOfRetries]);
            return $config[$numberOfRetries]*1000 ?? $config[6]*1000;
        };
    }
}