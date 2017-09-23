<?php

require '../vendor/autoload.php';
require 'Api.php';

use function Clue\React\Block\awaitAll;

// some synchronous staff ...

$eventLoop = \React\EventLoop\Factory::create();
$api = new Api($eventLoop);

$endpoint1 = 'https://api.github.com/repos/reactphp/event-loop';
$endpoint2 = 'https://api.github.com/repos/reactphp/promise';

$requests = [
	$api->get($endpoint1),
	$api->get($endpoint2),
];
$result = awaitAll($requests, $eventLoop);

print_r($result);

// some synchronous staff ...
