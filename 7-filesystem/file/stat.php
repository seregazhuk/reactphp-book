<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('new.txt')->stat()->then(
    function ($stat) {
        print_r($stat);
    }
);

$loop->run();
