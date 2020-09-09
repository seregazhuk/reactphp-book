<?php

require_once __DIR__ . '/vendor/autoload.php';

use Chat\Connections;
use Chat\Server;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('8080', $loop);
$server = new Server($socket, new Connections());
$server->run();

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
