<?php

namespace func\HelloWorld;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

abstract class RequestPause
{
    protected $logger;
    protected $client;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

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
        return Middleware::log( $this->logger,
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
            info($numberOfRetries.', '.$config[$numberOfRetries]);
            return $config[$numberOfRetries]*1000 ?? $config[6]*1000;
        };
    }
}