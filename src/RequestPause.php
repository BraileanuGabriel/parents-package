<?php

namespace Parents\RequestPause;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

abstract class RequestPause
{
    public $tries = 5;

    public function createHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        return $stack;
    }

    protected function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            $exception = null
        ) {

            if ($retries >= $this->tries) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
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
        $config = config('job_pause.pause_request_delay');
        return function ($numberOfRetries) use($config) {
            return $config[$numberOfRetries]*1000 ?? end($config)*1000;
        };
    }
}