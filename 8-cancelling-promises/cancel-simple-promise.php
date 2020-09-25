<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function React\Promise\Timer\timeout;

$loop = React\EventLoop\Factory::create();

$resolve = function (callable $resolve, callable $reject) use ($loop, &$timer) {
    $timer = $loop->addTimer(
        5,
        function () {
            echo "resolved\n";
        }
    );
};

$cancel = function (callable $resolve, callable $reject) use (&$timer) {
    echo "cancelled\n";
};

$promise = new React\Promise\Promise($resolve, $cancel);

timeout($promise, 2, $loop);

$loop->run();
