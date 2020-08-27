<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

final class Logger
{
    public function log(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then(
            function (Exception $error) {
                echo 'Error ' . $error->getMessage() . PHP_EOL;
            }
        );
    }
}

final class Provider
{
    private ReadableResourceStream $stream;

    public function __construct(string $path, LoopInterface $loop)
    {
        $this->stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );
    }

    public function getData(): PromiseInterface
    {
        return \React\Promise\Stream\buffer($this->stream);
    }

    public function getFirstError(): PromiseInterface
    {
        $promise = \React\Promise\Stream\first($this->stream, 'error');
        $this->stream->emit(
            'error',
            [new Exception('Something went wrong')]
        );

        return $promise;
    }
}

$loop = \React\EventLoop\Factory::create();

$logger = new Logger();
$provider = new Provider('file.txt', $loop);

$logger->log($provider->getFirstError());

$loop->run();
