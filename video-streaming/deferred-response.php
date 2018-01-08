<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) use ($loop) {
    return new Promise(function ($resolve) use ($loop) {
        $loop->addTimer(1.5, function() use ($resolve) {
            $response = new Response(
                200,
                array(
                    'Content-Type' => 'text/plain'
                ),
                'Hello world'
            );
            $resolve($response);
        });
    });
});


$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
