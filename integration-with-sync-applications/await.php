<?php

require '../vendor/autoload.php';
require 'Api.php';

use function Clue\React\Block\await; // some synchronous staff ...

$eventLoop = \React\EventLoop\Factory::create();
$api = new Api($eventLoop);

$endpoint = 'https://api.github.com/repos/reactphp/event-loop';
$result = await($api->get($endpoint), $eventLoop);

print_r($result);
// some synchronous staff ...
