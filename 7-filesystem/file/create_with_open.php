<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('new_3.txt');
$file->open('c')->then(
    fn() => print 'File created' . PHP_EOL
);

$loop->run();
