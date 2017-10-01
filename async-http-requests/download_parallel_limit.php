<?php

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Request;
use React\HttpClient\Response;
use React\Stream\WritableResourceStream;

class Downloader
{
	/**
	 * @var React\EventLoop\LoopInterface;
	 */
	private $loop;

	/**
	 * @var \React\HttpClient\Client
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $files = [];

	/**
	 * @var int
	 */
	protected $total;

	/**
	 * @var
	 */
	protected $done;

	/**
	 * @param Client $client
	 * @param LoopInterface $loop
	 */
	public function __construct(Client $client, LoopInterface $loop)
	{
		$this->client = $client;
		$this->loop = $loop;
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

		$max = $limit ? $limit : $this->total;
		while($max --) {
			$this->runDownload();
		}

		echo "Downloaded: 0%\n";
		$this->loop->run();
	}

	protected function runDownload()
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

		$file = new WritableResourceStream(fopen($fileName, 'w'), $this->loop);
		$request->on('response', function (Response $response) use ($file, $fileName) {
			$response->pipe($file);

			$response->on('end', function () use ($fileName) {
				$this->done++;
				$progress = number_format($this->done / $this->total * 100);
				echo "\033[1A Downloaded: $progress%\n";

				if ($this->files) $this->runDownload();
			});
		});

		return $request;
	}
}

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$files = [
	'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4',
	'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_2mb.mp4',
	'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_3mb.mp4',
	'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_4mb.mp4',
	'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_5mb.mp4',
];

(new Downloader($client, $loop))->download($files, 3);
