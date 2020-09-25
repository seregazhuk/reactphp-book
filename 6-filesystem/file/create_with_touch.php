<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->touch()->then(
    function () {
        echo 'File created' . PHP_EOL;
    }
);

$loop->run();
