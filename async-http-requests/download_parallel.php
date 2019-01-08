<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Filesystem\FilesystemInterface;
use React\HttpClient\Client;
use React\HttpClient\Response;
use function \React\Promise\Stream\unwrapWritable;
use React\Stream\ThroughStream;

final class Downloader
{
    private $client;

    private $filesystem;

    private $requests = [];

    public function __construct(Client $client, FilesystemInterface $filesystem)
    {
        $this->client = $client;
        $this->filesystem = $filesystem;
    }

    public function download(array $files)
    {
        foreach ($files as $index => $file) {
            $this->initRequest($file, $index + 1);
        }

        echo str_repeat("\n", count($this->requests));

        $this->runRequests();
    }

    /**
     * @param string $url
     * @param int $position
     */
    private function initRequest($url, $position)
    {
        $fileName = basename($url);
        $file = unwrapWritable($this->filesystem->file($fileName)->open('cw'));

        $request = $this->client->request('GET', $url);
        $request->on('response', function (Response $response) use ($file, $fileName, $position) {
            $size = $response->getHeaders()['Content-Length'];
            $progress = $this->makeProgressStream($size, $fileName, $position);
            $response->pipe($progress)->pipe($file);
        });

        $this->requests[] = $request;
    }

    /**
     * @param int $size
     * @param string $fileName
     * @param int $position
     * @return ThroughStream
     */
    private function makeProgressStream($size, $fileName, $position)
    {
        $currentSize = 0;

        $progress = new ThroughStream();
        $progress->on('data', function($data) use ($size, &$currentSize, $fileName, $position){
            $currentSize += strlen($data);
            echo str_repeat("\033[1A", $position), "$fileName: ", number_format($currentSize / $size * 100), "%", str_repeat("\n", $position);
        });

        return $progress;
    }

    private function runRequests()
    {
        foreach ($this->requests as $request) {
            $request->end();
        }

        $this->requests = [];
    }
}

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$files = [
    'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
    'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_2mb.mp4',
    'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_5mb.mp4',
];

$downloader = new Downloader($client, \React\Filesystem\Filesystem::create($loop));
$downloader->download($files);
$loop->run();
