<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

final class Processor
{
    public function process(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then(
            function (array $chunks) {
                echo 'Total chunks: ' . count($chunks) . PHP_EOL;
                foreach ($chunks as $index => $chunk) {
                    echo 'Chunk ' . ($index + 1) . ': ' . $chunk . PHP_EOL;
                }
            }
        );
    }
}

final class Provider
{
    public function get(string $path, LoopInterface $loop): PromiseInterface
    {
        $stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );

        return \React\Promise\Stream\all($stream);
    }
}

$loop = \React\EventLoop\Factory::create();

$processor = new Processor();
$provider = new Provider();

$processor->process($provider->get('file.txt', $loop))
    ->then(
        function () {
            echo 'Done' . PHP_EOL;
        }
    );

$loop->run();
