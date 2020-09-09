<?php

require_once __DIR__ . '/../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$deferred->resolve('my-value');

$promise = React\Promise\reject($deferred->promise());
$promise->then(
    null,
    fn($reason) => print "Promise was rejected with: $reason\n"
);
