<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\Promise\Timer\TimeoutException;

use function React\Promise\Timer\timeout;

$loop = React\EventLoop\Factory::create();

$resolve = function (callable $resolve, callable $reject) use ($loop, &$timer) {
    $timer = $loop->addTimer(
        5,
        function () use ($resolve) {
            $resolve();
        }
    );
};

$cancel = function (callable $resolve, callable $reject) use (&$timer) {
    echo "Principal promise: cancelled\n";
    /** @var \React\EventLoop\Timer\TimerInterface $timer */
    $timer->cancel();
};

$promise = new React\Promise\Promise($resolve, $cancel);

timeout($promise, 2, $loop)
    ->then(
        function () {
            // the principal promise resolved in 2 seconds
            echo "Timeout promise: Resolved before timeout.\n";
        }
    )
    ->otherwise(
        function (TimeoutException $exception) {
            // the principal promise cancelled due to a timeout
            echo "Timeout promise: Failed due to a timeout.\n";
        }
    )
    ->otherwise(
        function () {
            // the principal promise failed
            echo "Timeout promise: Failed to some error.\n";
        }
    );


$loop->run();
