<?php

require_once __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$eventLoop->futureTick(fn() => print "Tick\n");

echo "Loop starts\n";

$eventLoop->run();

echo "Loop stops\n";
