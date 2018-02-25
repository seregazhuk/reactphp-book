<?php

require './vendor/autoload.php';

use Clue\React\Buzz\Browser;
use React\EventLoop\LoopInterface;
use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    const DEFAULT_TIMEOUT = 5;

    /**
     * @var Browser
     */
    private $client;

    /**
     * @var array
     */
    private $parsed = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(Browser $client, LoopInterface $loop)
    {
        $this->client = $client;
        $this->loop = $loop;
    }

    public function parse(array $urls = [], $timeout = self::DEFAULT_TIMEOUT)
    {
        foreach ($urls as $url) {
            $promise = $this->client->get($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->parsed[] = $this->extractFromHtml((string) $response->getBody());
                }, function(Exception $e) use ($url) {
                    $this->errors[$url] = $e->getMessage();
                });

            $this->loop->addTimer($timeout, function() use ($promise, $url) {
                $promise->cancel();
            });
        }
    }

    public function extractFromHtml($html)
    {
        $crawler = new Crawler($html);

        $title = trim($crawler->filter('h1')->text());
        $genres = $crawler->filter('[itemprop="genre"] a')->extract(['_text']);
        $description = trim($crawler->filter('[itemprop="description"]')->text());

        $crawler->filter('#titleDetails .txt-block')->each(
            function (Crawler $crawler) {
                foreach ($crawler->children() as $node) {
                    $node->parentNode->removeChild($node);
                }
            }
        );

        $releaseDate = trim($crawler->filter('#titleDetails .txt-block')->eq(3)->text());

        return [
            'title'        => $title,
            'genres'       => $genres,
            'description'  => $description,
            'release_date' => $releaseDate,
        ];
    }

    public function movieData()
    {
        return $this->parsed;
    }

    public function errors()
    {
        return $this->errors;
    }
}


$loop = React\EventLoop\Factory::create();
$client = new Browser($loop);

$parser = new Parser($client, $loop);
$parser->parse([
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/'
], 3);

$loop->run();
print_r($parser->movieData());
print_r($parser->errors());
