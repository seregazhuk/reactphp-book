<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$redirectMiddleware = function(ServerRequestInterface $request, callable $next) {
    if($request->getUri()->getPath() === '/admin') {
        return new Response(301, ['Location' => '/']);
    }
    return $next($request);
};

$clientIpMiddleware = function(ServerRequestInterface $request, callable $next) {
    $serverParams = $request->getServerParams();

    if (!empty($serverParams['HTTP_CLIENT_IP'])) {
        $clientIp = $serverParams['HTTP_CLIENT_IP'];
    } elseif (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
        $clientIp = $serverParams['HTTP_X_FORWARDED_FOR'];
    } else {
        $clientIp = $serverParams['REMOTE_ADDR'];
    }


    return $next($request->withAttribute('client-ip', $clientIp));
};

$loggingMiddleware = function(ServerRequestInterface $request, callable $next) {
    $logData = [
        date('Y-m-d H:i:s'),
        $request->getAttribute('client-ip'),
        $request->getMethod(),
        $request->getUri()->getPath()
    ];
    echo implode(' ', $logData) . PHP_EOL;

    return $next($request);
};

$server = new Server([
    $loggingMiddleware,
    $redirectMiddleware,
    function (ServerRequestInterface $request) {
        return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
