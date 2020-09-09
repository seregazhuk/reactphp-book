<?php

require_once __DIR__ . '/../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise(),
];

$promise = \React\Promise\any($pending)->then(
    fn($resolved) => print $resolved . PHP_EOL
);

$loop = \React\EventLoop\Factory::create();
$loop->addTimer(2, fn() => $firstResolver->resolve(10));
$loop->addTimer(1, fn() => $secondResolver->resolve(20));

$loop->run();
