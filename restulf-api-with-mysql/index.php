<?php

use React\Http\Server;
use React\MySQL\Factory;

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:@localhost/reactphp-users');
$users = new \App\Users($db);

$router = new \App\Router(function(FastRoute\RouteCollector $routes) use ($users) {
    $routes->addRoute('GET', '/users', new \App\Controller\ListUsers($users));
    $routes->addRoute('POST', '/users', new \App\Controller\CreateUser($users));
    $routes->addRoute('GET', '/users/{id}', new \App\Controller\ViewUser($users));
    $routes->addRoute('PUT', '/users/{id}', new \App\Controller\UpdateUser($users));
    $routes->addRoute('DELETE', '/users/{id}', new \App\Controller\DeleteUser($users));
});

$server = new Server([
    new \App\Guard($loop, ['root' => 'root']),
    $router
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

$server->on('error', function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
