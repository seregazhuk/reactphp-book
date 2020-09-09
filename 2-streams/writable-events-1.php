<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$writable = new \React\Stream\WritableResourceStream(STDOUT, $loop, 1);

var_dump($writable->write("Hello world\n"));

$writable->on(
    'drain',
    fn () => print "The stream is drained\n"
);

$loop->run();
