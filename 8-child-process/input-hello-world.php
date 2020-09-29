<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\ChildProcess\Process;
use React\EventLoop\Factory;

$loop = Factory::create();
$process = new Process('php -a');

$process->start($loop);
$process->stdout->on(
    'data',
    function ($data) {
        echo $data;
    }
);

$process->stdin->write("echo 'Hello World';\n");
$loop->run();

