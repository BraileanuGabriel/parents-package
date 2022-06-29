<?php

namespace Parents\RequestPause;

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

    public $tries = 5;

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
        $stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        return $stack;
    }

    protected function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
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
        $config = config('job_pause.pause_job_delay');
        return function ($numberOfRetries) use($config) {
            info($numberOfRetries.' and '.$config[$numberOfRetries]);
            return $config[$numberOfRetries]*1000 ?? $config[6]*1000;
        };
    }
}