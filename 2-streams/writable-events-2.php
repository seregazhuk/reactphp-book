<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$writable = new \React\Stream\WritableResourceStream(STDOUT, $loop);

// This code will never be executed
$writable->on('end', fn () => print "End\n");

$writable->on('close', fn () => print "Close\n");

$loop->addTimer(1, fn() => $writable->end());

$loop->run();
