<?php

require '../vendor/autoload.php';

use React\HttpClient\Client;
use React\EventLoop\Factory as EventLoopFactory; use React\HttpClient\Response;
use Clue\React\Block;

// some synchronous code ...

$eventLoop = EventLoopFactory::create();
$client = new Client($eventLoop);
$endpoint = 'https://api.github.com/repos/reactphp/event-loop';

$request = $client->request('GET', $endpoint);
$request->on('response', function (Response $response) {
	$response->on('data', function ($chunk) {
		echo $chunk;
	});
});
$request->end();

// run an event loop for 2 seconds
Block\sleep(2, $eventLoop);

// some synchronous code ...
