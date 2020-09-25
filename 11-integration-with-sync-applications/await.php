<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Browser;

use function Clue\React\Block\await;

// some synchronous staff ...

$eventLoop = Factory::create();
$api = new Browser($eventLoop);

$endpoint = 'https://api.github.com/repos/reactphp/eeeeevent-loop';
$result = await($api->get($endpoint), $eventLoop);

echo $result->getBody() . PHP_EOL;
// some synchronous staff ...
