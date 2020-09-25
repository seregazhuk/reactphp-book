<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Clue\React\Block\awaitAny;

// some synchronous staff ...

$eventLoop = \React\EventLoop\Factory::create();
$browser = new \React\Http\Browser($eventLoop);

$endpoint1 = 'https://api.github.com/repos/reactphp/event-loop';
$endpoint2 = 'https://api.github.com/repos/reactphp/promise';

$requests = [
    $browser->get($endpoint1),
    $browser->get($endpoint2),
];
$result = awaitAny($requests, $eventLoop);

echo $result->getBody() . PHP_EOL;
// some synchronous staff ...
