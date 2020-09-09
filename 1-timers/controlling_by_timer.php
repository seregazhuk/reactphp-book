<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$counter = 0;
$periodicTimer = $loop->addPeriodicTimer(
    2,
    function () use (&$counter) {
        $counter++;
        echo "$counter\n";
    }
);

$loop->addTimer(5, fn() => $loop->cancelTimer($periodicTimer));

$loop->run();
