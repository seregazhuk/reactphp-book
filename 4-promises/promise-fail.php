<?php

require_once __DIR__ . '/../vendor/autoload.php';

$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->otherwise(
    fn ($data) => print 'Fail: ' . $data . PHP_EOL
);

$deferred->reject('no results');
