<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir('temp');

$dir->removeRecursive()->then(
    fn() => print "Removed\n",
    fn(Exception $e) => print "Error: {$e->getMessage()}\n"
);

$loop->run();
