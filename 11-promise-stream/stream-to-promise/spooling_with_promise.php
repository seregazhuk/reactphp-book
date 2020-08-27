<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

final class Processor
{
    public function process(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then('trim')
            ->then(
                function ($string) {
                    return str_replace(' ', '-', $string);
                }
            )
            ->then('strtolower');
    }
}

final class Provider
{
    public function get(string $path, LoopInterface $loop): PromiseInterface
    {
        $stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );

        return \React\Promise\Stream\buffer($stream);
    }
}

$loop = \React\EventLoop\Factory::create();

$processor = new Processor();
$provider = new Provider();

$processor
    ->process($provider->get('file.txt', $loop))
    ->then(
        function ($data) {
            echo $data . PHP_EOL;
            echo 'Done' . PHP_EOL;
        }
    );

$loop->run();
