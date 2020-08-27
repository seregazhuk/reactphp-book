<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../simple-server/ConnectionsPool.php';

use React\Socket\ConnectionInterface;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server(8080, $loop);
$pool = new ConnectionsPool();

$socket->on(
    'connection',
    function (ConnectionInterface $connection) use ($pool) {
        $pool->add($connection);
    }
);

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
