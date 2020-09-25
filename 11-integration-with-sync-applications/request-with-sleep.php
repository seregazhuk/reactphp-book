<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Clue\React\Block;
use React\EventLoop\Factory;
use React\Http\Browser;

// some synchronous code ...

$eventLoop = Factory::create();
$browser = new Browser($eventLoop);
$endpoint = 'https://api.github.com/repos/reactphp/event-loop';

$promise = $browser->get($endpoint);
$promise->then(
    function ($response) {
        echo $response->getBody();
    }
);
// run an event loop for 2 seconds
Block\sleep(2, $eventLoop);


// some synchronous code ...
