<?php

namespace func\HelloWorld;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;

class RequestPause
{
    protected function createHandlerStack($tries): HandlerStack
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry(function () use($tries) {return $tries;}, $this->retryDelay()));
        return $this->createLoggingHandlerStack($stack);
    }

    protected function createLoggingHandlerStack(HandlerStack $stack): HandlerStack
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

    protected function createGuzzleLoggingMiddleware(string $messageFormat): callable
    {
        return Middleware::log( null,
            new MessageFormatter($messageFormat)
        );
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
                case 1: return $config[1]*1000;
                case 2: return $config[2]*1000;
                case 3: return $config[3]*1000;
                case 4: return $config[4]*1000;
                case 5: return $config[5]*1000;
                default:
                    return $config[6]*1000;
            }
        };
    }
}