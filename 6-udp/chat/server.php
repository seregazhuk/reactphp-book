<?php

use React\Datagram\Socket;
use React\EventLoop\LoopInterface;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Output.php';
require_once __DIR__ . '/Action.php';

final class UdpChatServer
{
    private array $clients = [];
    private Socket $socket;
    private string $address;
    private LoopInterface $loop;
    private Output $output;

    public function __construct(string $address, LoopInterface $loop)
    {
        $this->address = $address;
        $this->loop = $loop;
        $this->output = new Output();
    }

    private function process(string $data, string $address): void
    {
        $data = json_decode($data, true);

        $action = new Action($data['type']);
        if ($action->isEnter()) {
            $this->addClient($data['name'], $address);
            return;
        }

        if ($action->isLeave()) {
            $this->removeClient($address);
            return;
        }

        $this->sendMessage($data['message'], $address);
    }

    private function addClient(string $name, string $address): void
    {
        if (array_key_exists($address, $this->clients)) {
            return;
        }

        $this->clients[$address] = $name;
        $this->broadcast("$name enters chat", $address);
    }

    private function removeClient(string $address): void
    {
        $name = $this->clients[$address] ?? '';
        unset($this->clients[$address]);

        $this->broadcast("$name leaves chat");
    }

    private function broadcast(string $message, string $except = null): void
    {
        foreach ($this->clients as $address => $name) {
            if ($address !== $except) {
                $this->socket->send($message, $address);
            }
        }
    }

    private function sendMessage(string $message, string $address): void
    {
        $name = $this->clients[$address] ?? '';

        $this->broadcast($this->output->message($name, $message), $address);
    }

    public function run(): void
    {
        $factory = new React\Datagram\Factory($this->loop);
        $factory->createServer($this->address)
            ->then(
                function (React\Datagram\Socket $server) {
                    $this->socket = $server;
                    $server->on(
                        'message',
                        function ($data, $client) {
                            $this->process($data, $client);
                        }
                    );
                },
                function (Exception $error) {
                    echo "ERROR: {$error->getMessage()}\n";
                }
            );

        echo "Listening on $this->address\n";
        $this->loop->run();
    }
}

$loop = React\EventLoop\Factory::create();
(new UdpChatServer('localhost:1234', $loop))->run();

