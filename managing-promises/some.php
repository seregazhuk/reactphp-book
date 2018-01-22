<?php

require '../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();
$thirdResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise(),
    $thirdResolver->promise(),
];

$promise = \React\Promise\some($pending, 2)
    ->then(function($resolved){
        echo 'Resolved' . PHP_EOL;
        print_r($resolved);
    });

$firstResolver->resolve(1);
$secondResolver->resolve(2);
$thirdResolver->resolve(3);


