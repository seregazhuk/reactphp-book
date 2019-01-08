<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);
$filesystem = \React\Filesystem\Filesystem::create($loop);
$file = \React\Promise\Stream\unwrapWritable($filesystem->file('sample.mp4')->open('cw'));

$request = $client->request('GET', 'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4');

$request->on('response', function (\React\HttpClient\Response $response) use ($file) {
    $size = $response->getHeaders()['Content-Length'];
    $currentSize = 0;

    $progress = new \React\Stream\ThroughStream();
    $progress->on('data', function($data) use ($size, &$currentSize){
        $currentSize += strlen($data);
        echo "\033[1A", 'Downloading: ', number_format($currentSize / $size * 100), "%\n";
    });

    $response->pipe($progress)->pipe($file);
});

$request->end();
echo "\n";
$loop->run();
