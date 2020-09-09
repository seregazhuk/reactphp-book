<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('new.txt')
    ->size()
    ->then(
        fn($size) => print "Size is: $size bytes\n"
    );

$loop->run();
