<?php

use React\EventLoop\LoopInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$callback = function (LoopInterface $eventLoop) use (&$callback) {
    echo "Hello world\n";
    $eventLoop->futureTick($callback);
};

$eventLoop->futureTick($callback);
$eventLoop->futureTick(fn(LoopInterface $eventLoop) => $eventLoop->stop());

$eventLoop->run();
echo "Finished\n";
