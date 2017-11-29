<?php

use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Response;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class Api
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param LoopInterface $eventLoop
     */
    public function __construct(LoopInterface $eventLoop)
    {
        $this->client = new Client($eventLoop);
    }

    /**
     * @param string $url
     * @return PromiseInterface
     */
    public function get($url)
    {
        $request = $this->client->request('GET', $url);
        return new Promise(
            function ($resolve, $reject) use ($request) {
                $request->on('response', $this->attachResponseHandlers(
                    $resolve, $reject
                ));
                $request->end();
            }
        );
    }

    protected function attachResponseHandlers($resolve, $reject)
    {
        return function (Response $response) use ($resolve, $reject) {
            if ($response->getCode() != 200) {
                $reject(new Exception('something went wrong'));
            }
            $response
                ->on('data', function ($chunk) use (&$result) {
                    $result .= $chunk;
                })
                ->on('end', function () use ($resolve, &$result) {
                    $resolve(json_decode($result, TRUE));
                });
        };
    }
}
