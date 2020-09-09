<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('test.txt')
    ->chown('serega')
    ->then(
        fn() => print 'Owner changed' . PHP_EOL,
        fn(Exception $e) => print "Error: {$e->getMessage()}\n"
);

$loop->run();
