<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('test.txt')
    ->chmod(0755)
    ->then(
        fn() => print "Mode changed\n"
    );

$loop->run();
