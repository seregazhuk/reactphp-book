<?php
require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) {
    $key = 'reactPHP';
    $cookieValue = $request->getCookieParams()[$key] ?? null;

    if ($cookieValue) {
        return new Response(
            200,
            ['Content-Type' => 'text/plain'],
            'Your cookie value is: ' . $cookieValue
        );
    }

    return new Response(
        200,
        [
            'Content-Type' => 'text/plain',
            'Set-Cookie' => urlencode($key) . '=' . urlencode('test')
        ],
        'Your cookie has been set.'
    );
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
