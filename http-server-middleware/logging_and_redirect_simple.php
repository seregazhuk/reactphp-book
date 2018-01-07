<?php
require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) {
    if($request->getUri()->getPath() === '/admin') {
        return new Response(301, ['Location' => '/']);
    }

    $serverParams = $request->getServerParams();

    if (!empty($serverParams['HTTP_CLIENT_IP'])) {
        $clientIp = $serverParams['HTTP_CLIENT_IP'];
    } elseif (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
        $clientIp = $serverParams['HTTP_X_FORWARDED_FOR'];
    } else {
        $clientIp = $serverParams['REMOTE_ADDR'];
    }

    $logData = [
        $clientIp,
        $request->getMethod(),
        $request->getUri()->getPath()
    ];

    echo date('Y-m-d H:i:s') . ' '
        . implode(' ', $logData)
        . PHP_EOL;

    return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
