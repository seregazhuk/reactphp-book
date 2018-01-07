<?php

require '../vendor/autoload.php';

use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use React\EventLoop\Factory;
use React\Http\Server;

$loop = Factory::create();

$server = new Server([
    new PSR15Middleware($loop, \Middlewares\ClientIp::class),
    function(\Psr\Http\Message\RequestInterface $request, $next){
        echo 'IP: ' . $request->getAttribute('client-ip') . PHP_EOL;
        return $next($request);
    },
    new PSR15Middleware(
        $loop,
        \Middlewares\Redirect::class,
        [
            ['/admin' => '/']
        ]
    ),
    function() {
        return new \React\Http\Response(200);
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

$server->on('error', function ($et) {
    echo (string)$et;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";
$loop->run();


