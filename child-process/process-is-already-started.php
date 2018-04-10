<?php

require '../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('php -a');

$process->start($loop);
$process->stdout->on('data', function($data) {
    echo $data;
});

$process->stdin->write("echo 'Hello World';\n");

// RuntimeException will be thrown
$process->start($loop);
$loop->run();

