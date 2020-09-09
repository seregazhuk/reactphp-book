<?php

require_once __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$string = "Tick!\n";
$eventLoop->futureTick(fn() => print $string);

echo "Loop starts\n";

$eventLoop->run();

echo "Loop stops\n";
