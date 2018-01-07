<?php

require '../vendor/autoload.php';

use FriendsOfReact\Http\Middleware\Psr15Adapter\GroupedPSR15Middleware;
use React\EventLoop\Factory;
use React\Http\Server;

$loop = Factory::create();

$server = new Server([
    (new GroupedPSR15Middleware($loop))
        ->withMiddleware(\Middlewares\ClientIp::class)
        ->withMiddleware(
            \Middlewares\Redirect::class,
            [
                ['/old-url' => '/new-url']
            ]),
    function(\Psr\Http\Message\RequestInterface $request){
        echo 'IP: ' . $request->getAttribute('client-ip');
        return new \React\Http\Response(200);
    }
]);


$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";
$loop->run();


