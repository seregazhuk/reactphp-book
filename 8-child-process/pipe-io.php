<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\ChildProcess\Process;
use React\EventLoop\Factory;

$loop = Factory::create();
$process = new Process('ls | wc -l');

$process->start($loop);

$process->stdout->on(
    'data',
    function ($data) {
        echo 'Total number of files and folders :' . $data;
    }
);

$loop->run();
