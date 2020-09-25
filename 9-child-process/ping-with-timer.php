<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\ChildProcess\Process;
use React\EventLoop\Factory;

$loop = Factory::create();
$process = new Process('ping 8.8.8.8');

$process->start($loop);
$process->stdout->on(
    'data',
    function ($data) {
        echo $data;
    }
);

$loop->addTimer(3, fn() => $process->terminate());
$process->on('exit', fn() => print 'Process exited');

$loop->run();
