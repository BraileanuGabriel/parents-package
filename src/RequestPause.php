<?php

namespace func\HelloWorld;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

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
        $stack->push(Middleware::retry($this->retryDecider($tries), $this->retryDelay()));
        return $stack;
    }

    protected function retryDecider($tries)
    {
        return function (
            $retries,
            Request $request,
            Response $response = null
        ) use($tries) {

            if ($retries >= $tries) {
                return false;
            }

            if ($response) {
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }
            return false;
        };
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