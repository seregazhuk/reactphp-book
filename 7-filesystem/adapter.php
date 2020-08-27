<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
echo 'Using ' . get_class($filesystem->getAdapter()) . PHP_EOL;

$loop->run();
