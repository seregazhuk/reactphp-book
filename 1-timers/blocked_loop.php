<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$i = 0;

$loop->addPeriodicTimer(1, function() use (&$i) {
	echo ++$i, "\n";
});

$loop->addTimer(2, fn () => sleep(10));

$loop->run();
