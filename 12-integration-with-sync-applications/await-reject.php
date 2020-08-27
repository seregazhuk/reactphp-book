<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Browser;

use function Clue\React\Block\await;

// some synchronous staff ...

$eventLoop = Factory::create();
$browser = new Browser($eventLoop);

$endpoint = 'https://api.github.com/repos/reactphp/eeeeevent-loop';
try {
	// promise successfully fulfilled with $result
	$result = await($browser->get($endpoint), $eventLoop);
	echo $result->getBody() . PHP_EOL;
} catch (Exception $exception) {
	// promise rejected with $exception
	echo 'ERROR: ' . $exception->getMessage();
}

// some synchronous staff ...
