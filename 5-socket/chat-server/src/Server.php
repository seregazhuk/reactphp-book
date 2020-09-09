<?php

declare(strict_types=1);

namespace Chat;

use React\Socket\ConnectionInterface;
use React\Socket\Server as SocketServer;

final class Server
{
    private SocketServer $socket;
    private Connections $connections;

    public function __construct(SocketServer $socket, Connections $connections)
    {
        $this->socket = $socket;
        $this->connections = $connections;
    }

    public function run(): void
    {
        $this->socket->on(
            'connection',
            function (ConnectionInterface $connection) {
                $connection->write("Enter your name: ");
                $this->subscribeToEvents($connection);
            }
        );
    }

    private function subscribeToEvents(ConnectionInterface $connection): void
    {
        $connection->on(
            'data',
            fn($data) => $this->onDataReceived($connection, $data)
        );

        $connection->on(
            'close',
            fn() => $this->onUserLeaves($connection)
        );
    }

    private function onDataReceived(ConnectionInterface $connection, string $data): void
    {
        if ($this->connections->exists($connection)) {
            $this->sendChatMessage($connection, $data);
            return;
        }

        $this->addNewMember($data, $connection);
    }

    private function addNewMember(string $username, ConnectionInterface $connection): void
    {
        $username = trim($username);
        if ($this->connections->hasWithUsername($username)) {
            $data = Message::warning("Name $username is already taken!\n")->toString();
            $connection->write($data);
            $connection->write("Enter your name: ");
            return;
        }

        $message = Message::info("$username enters the chat\n");
        $this->broadcast($message, $connection);
        $this->connections->add($connection, $username);
    }

    private function onUserLeaves(ConnectionInterface $connection): void
    {
        $username = $this->connections->getUsername($connection);
        $this->connections->remove($connection);
        $message = Message::info("$username leaves the chat\n");
        $this->broadcast($message);
    }

    /**
     * Send data to all connections from the
     * pool except the specified one.
     */
    private function broadcast(Message $message, ConnectionInterface $except = null): void
    {
        foreach ($this->connections as $conn) {
            if ($conn !== $except) {
                $conn->write($message->toString());
            }
        }
    }

    private function sendChatMessage(ConnectionInterface $connection, string $data): void
    {
        $username = $this->connections->getUsername($connection);
        $message = Message::from($data, $username);
        if ($message->isPublic()) {
            $this->broadcast($message, $connection);
            return;
        }

        $this->sendPrivateMessage($message, $connection);
    }

    private function sendPrivateMessage(Message $message, ConnectionInterface $connection): void
    {
        $to = $this->connections->getByUserName($message->to());
        if ($to !== null) {
            $to->write($message->toString());
            return;
        }

        $warning = Message::warning(
            "User {$message->to()} not found."
        );
        $connection->write($warning->toString());
    }
}
