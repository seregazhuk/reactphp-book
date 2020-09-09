<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$readable = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop, 1);
$output = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$readable->on(
    'data',
    fn ($data) => $output->write($data)
);

$readable->on(
    'end',
    function () use ($output) {
        $output->end();
        $output->write('Hello!');
    }
);

$loop->run();
