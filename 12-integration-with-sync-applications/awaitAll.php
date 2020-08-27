<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Browser;

use function Clue\React\Block\awaitAll;

// some synchronous staff ...

$eventLoop = Factory::create();
$browser = new Browser($eventLoop);

$endpoint1 = 'https://api.github.com/repos/reactphp/event-loop';
$endpoint2 = 'https://api.github.com/repos/reactphp/promise';

$requests = [
	$browser->get($endpoint1),
	$browser->get($endpoint2),
];
$results = awaitAll($requests, $eventLoop);

echo $results[0]->getBody() . PHP_EOL; // result for $endpoint1
echo $results[1]->getBody() . PHP_EOL; // result for $endpoint2
// some synchronous staff ...
