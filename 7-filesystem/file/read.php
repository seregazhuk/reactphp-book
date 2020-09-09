<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->getContents()->then(
    fn ($contents) => print 'Reading completed' . PHP_EOL
);
$loop->addPeriodicTimer(1, fn() => print 'Timer' . PHP_EOL);

$loop->run();
