<?php

require_once __DIR__ . '/../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise(),
];

$promise = \React\Promise\race($pending)
    ->then(
        fn($resolved) => print 'Resolved with: ' . $resolved . PHP_EOL,
        fn($reason) => print 'Failed with: ' . $reason . PHP_EOL
    );

$loop = \React\EventLoop\Factory::create();
$loop->addTimer(2, fn() => $firstResolver->resolve(10));
$loop->addTimer(1, fn() => $secondResolver->reject(20));

$loop->run();
