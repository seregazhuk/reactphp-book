<?php

require_once __DIR__ . '/../vendor/autoload.php';

$promise = $deferred->promise();
$promise->done(
    function ($data) {
        throw new Exception('error');
    }
);

$deferred->resolve('no results');
