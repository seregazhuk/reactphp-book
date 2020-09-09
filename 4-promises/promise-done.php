<?php

require_once __DIR__ . '/../vendor/autoload.php';

$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->done(
    fn ($data) => print 'Done: ' . $data . PHP_EOL
);

$deferred->resolve('hello world');
