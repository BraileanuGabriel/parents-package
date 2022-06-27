<?php

namespace func\HelloWorld;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class RequestPause
{
    const MAX_RETRIES = 3;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function createHandlerStack($tries)
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry(function () use($tries) {return $tries;}, $this->retryDelay()));
        return $this->createLoggingHandlerStack($stack);
    }

    protected function createLoggingHandlerStack(HandlerStack $stack)
    {
        $messageFormats = [
            '{method} {uri} HTTP/{version}',
            'HEADERS: {req_headers}',
            'BODY: {req_body}',
            'RESPONSE: {code} - {res_body}',
        ];
        foreach ($messageFormats as $messageFormat) {
            // We'll use unshift instead of push, to add the middleware to the bottom of the stack, not the top
            $stack->unshift(
                $this->createGuzzleLoggingMiddleware($messageFormat)
            );
        }

        return $stack;
    }

    protected function createGuzzleLoggingMiddleware(string $messageFormat)
    {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
    }

    protected function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) {
            // Limit the number of retries to MAX_RETRIES
            if ($retries >= self::MAX_RETRIES) {
                return false;
            }
            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }
            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }
            return false;
        };
    }
    /**
     * delay 1s 2s 3s 4s 5s ...
     *
     * @return callable
     */
    protected function retryDelay()
    {
        $config = config('job_pause.pause_job_delay');
        return function ($numberOfRetries) use($config) {
            switch ($numberOfRetries){
                case 1: return $config[1];
                case 2: return $config[2];
                case 3: return $config[3];
                case 4: return $config[4];
                case 5: return $config[5];
                case $numberOfRetries>5: return $config[6];
            }
        };
    }
}