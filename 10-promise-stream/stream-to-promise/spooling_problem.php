<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;

final class Processor
{
    public function process(string $data): void
    {
        echo $data . PHP_EOL;
        echo 'Done' . PHP_EOL;
    }
}

final class Provider
{
    public function get(string $path, LoopInterface $loop)
    {
        $spool = '';

        $stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );

        $stream->on(
            'data',
            function ($data) use (&$spool) {
                $spool .= $data;
            }
        );

        $stream->on(
            'end',
            function () use (&$spool) {
                echo $spool;
                // ???
            }
        );
    }
}

$loop = \React\EventLoop\Factory::create();

(new Provider())->get('file.txt', $loop);

$loop->run();
