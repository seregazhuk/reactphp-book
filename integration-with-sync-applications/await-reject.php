<?php

require '../vendor/autoload.php';
require 'Api.php';

use function Clue\React\Block\await;

// some synchronous staff ...

$eventLoop = \React\EventLoop\Factory::create();
$api = new Api($eventLoop);

$endpoint = 'https://api.github.com/repos/reactphp/eeeeevent-loop';
try {
	// promise successfully fulfilled with $result
	$result = await($api->get($endpoint), $eventLoop);
} catch (\Exception $exception) {
	// promise rejected with $exception
	echo 'ERROR: ' . $exception->getMessage();
}

// some synchronous staff ...
