<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Filesystem\FilesystemInterface;
use React\HttpClient\Client;
use React\HttpClient\Request;
use React\HttpClient\Response;

class Downloader
{
	private $client;

    private $filesystem;

    private $files = [];

    private $total;

    private $done;

	public function __construct(Client $client, FilesystemInterface $filesystem)
	{
		$this->client = $client;
        $this->filesystem = $filesystem;
    }

	/**
	 * @param array $files
	 * @param int $limit
	 */
	public function download(array $files, $limit = 0)
	{
		$this->files = $files;
		$this->total = count($files);
		$this->done = 0;

		$max = $limit ?: $this->total;
		while($max --) {
			$this->runDownload();
		}

		echo "Downloaded: 0%\n";
	}

	private function runDownload()
	{
		$file = array_pop($this->files);
		$request = $this->initRequest($file);
		$request->end();
	}

	/**
	 * @param string $url
	 * @return Request
	 */
	public function initRequest($url)
	{
		$request = $this->client->request('GET', $url);
		$fileName = basename($url);

		$file = \React\Promise\Stream\unwrapWritable($this->filesystem->file($fileName)->open('cw'));
		$file->on('error', function (Exception $exception) {
		    echo $exception->getMessage() . PHP_EOL;
        });
		$request->on('response', function (Response $response) use ($file) {
			$response->pipe($file);

			$response->on('end', function () {
				$this->done++;
				$progress = number_format($this->done / $this->total * 100);
				echo "\033[1A Downloaded: $progress%\n";

				if ($this->files) {
                    $this->runDownload();
                }
			});
		});

		return $request;
	}
}

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$files = [
	'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
	'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_2mb.mp4',
	'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_5mb.mp4',
	'https://www.sample-videos.com/video123/mp4/720/big_buck_bunny_720p_10mb.mp4',
];

$downloader = new Downloader($client, \React\Filesystem\Filesystem::create($loop));
$downloader->download($files, 3);

$loop->run();
