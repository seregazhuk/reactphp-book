<?php

require_once __DIR__ . '/../vendor/autoload.php';

$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->otherwise(
    function ($data) {
        echo 'Fail: ' . $data . PHP_EOL;
    }
);

$deferred->reject('no results');
