<?php

use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableStreamInterface;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Action.php';

final class UdpChatClient
{
    private LoopInterface $loop;

    private ReadableStreamInterface $stdin;

    private Socket $socket;

    private string $name = '';

    private string $address;

    public function __construct(string $address, LoopInterface $loop)
    {
        $this->address = $address;
        $this->loop = $loop;
    }

    public function run(): void
    {
        $factory = new React\Datagram\Factory($this->loop);
        $this->stdin = new React\Stream\ReadableResourceStream(STDIN, $this->loop);
        $this->stdin->on(
            'data',
            function ($data) {
                $this->processInput($data);
            }
        );

        $factory->createClient($this->address)
            ->then(
                function (Socket $client) {
                    $this->initClient($client);
                },
                function (Exception $error) {
                    echo "ERROR: {$error->getMessage()}\n";
                }
            );

        $this->loop->run();
    }

    private function initClient(React\Datagram\Socket $client): void
    {
        $this->socket = $client;

        $this->socket->on(
            'message',
            function ($message) {
                echo $message . "\n";
            }
        );

        $this->socket->on(
            'close',
            function () {
                $this->loop->stop();
            }
        );

        echo "Enter your name: ";
    }

    private function processInput(string $data): void
    {
        $data = trim($data);

        if (empty($this->name)) {
            $this->name = $data;
            $this->sendData(Action::enter());
            return;
        }

        if ($data === ':exit') {
            $this->sendData(Action::leave());
            $this->socket->end();
            return;
        }

        $this->sendData(Action::message($data));
    }

    private function sendData(Action $action): void
    {
        $data = $action->toArray($this->name);

        $this->socket->send(json_encode($data));
    }
}

$loop = React\EventLoop\Factory::create();
(new UdpChatClient('localhost:1234', $loop))->run();
