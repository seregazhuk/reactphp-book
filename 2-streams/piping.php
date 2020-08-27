<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$readable = new React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop, 1);
$output = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$readable->pipe($output);

$loop->run();
