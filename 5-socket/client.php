<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\Socket\ConnectionInterface;

$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\Connector($loop);

$input = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$output = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$connector
    ->connect('127.0.0.1:8080')
    ->then(
        fn (ConnectionInterface $conn) => $input->pipe($conn)->pipe($output),
        function (Exception $exception) use ($loop) {
            echo "Cannot connect to server: " . $exception->getMessage();
            $loop->stop();
        }
    );

$loop->run();

