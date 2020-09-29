<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\ChildProcess\Process;
use React\EventLoop\Factory;

$loop = Factory::create();
$ls = new Process('ls'); // list files in current directory
$wc = new Process('wc -l'); // counts number of lines

$ls->start($loop);
$wc->start($loop);

$ls->stdout->pipe($wc->stdin);

$wc->stdout->on(
    'data',
    function ($data) {
        echo 'Total number of files and folders: ' . $data;
    }
);

$loop->run();

