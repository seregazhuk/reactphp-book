<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir('temp');

$dir->removeRecursive()->then(
    function () {
        echo 'Removed' . PHP_EOL;
    },
    function (Exception $e) {
        echo 'Error: ' . $e->getMessage() . PHP_EOL;
    }
);

$loop->run();
