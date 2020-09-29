<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$resolve = function (callable $resolve, callable $reject) use ($loop) {
    $loop->addTimer(5, fn() => $resolve('Hello world!'));
};

$cancel = function (callable $resolve, callable $reject) {
    $reject(new \Exception('Promise cancelled!'));
};

$promise = new React\Promise\Promise($resolve, $cancel);
$promise->then(fn($value) => print $value);

$loop->run();
