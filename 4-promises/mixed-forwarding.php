<?php

require_once __DIR__ . '/../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();

$deferred->promise()
    ->then(
        function ($data) {
            echo $data . PHP_EOL;
            return $data . ' world';
        }
    )
    ->then(
        function ($data) {
            throw new Exception('error: ' . $data);
        }
    )
    ->otherwise(
        function (Exception $e) {
            return $e->getMessage();
        }
    )
    ->then(
        function ($data) {
            echo $data . PHP_EOL;
        }
    );

$deferred->resolve('hello');
