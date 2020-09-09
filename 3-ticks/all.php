<?php

require_once __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$eventLoop->addTimer(0, fn() => print "Timer\n");
$eventLoop->futureTick(fn() => print "Future tick\n");

$writable = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $eventLoop);
$writable->write("I\O");

$eventLoop->run();
