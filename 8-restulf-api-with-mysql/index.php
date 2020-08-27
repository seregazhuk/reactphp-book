<?php

use App\Auth;
use App\Controller\CreateUser;
use App\Controller\DeleteUser;
use App\Controller\ListUsers;
use App\Controller\UpdateUser;
use App\Controller\ViewUser;
use App\Router;
use App\Users;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\Http\Server;
use React\MySQL\Factory;

require_once __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:@localhost/reactphp-users');
$users = new Users($db);

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/users', new ListUsers($users));
$routes->post('/users', new CreateUser($users));
$routes->get('/users/{id}', new ViewUser($users));
$routes->put('/users/{id}', new UpdateUser($users));
$routes->delete('/users/{id}', new DeleteUser($users));

$server = new Server(
    $loop,
    new Auth($loop, ['user' => 'secret']),
    new Router($routes),
);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

$server->on(
    'error',
    function (Exception $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
