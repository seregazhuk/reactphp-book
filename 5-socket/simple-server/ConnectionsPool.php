<?php

use React\Socket\ConnectionInterface;

class ConnectionsPool
{
    private SplObjectStorage $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    public function add(ConnectionInterface $connection): void
    {
        $connection->write("Enter your name: ");
        $this->initEvents($connection);
        $this->setConnectionData($connection, []);
    }

    private function initEvents(ConnectionInterface $connection): void
    {
        // On receiving the data we loop through other connections
        // from the pool and write this data to them
        $connection->on(
            'data',
            function ($data) use ($connection) {
                $connectionData = $this->getConnectionData($connection);

                // It is the first data received, so we consider it as
                // a users name.
                if (empty($connectionData['name'])) {
                    $this->addNewMember($data, $connection);
                    return;
                }

                $name = $connectionData['name'];
                $this->sendAll("$name: $data", $connection);
            }
        );

        // When connection closes detach it from the pool
        $connection->on(
            'close',
            function () use ($connection) {
                $data = $this->getConnectionData($connection);
                $name = $data['name'] ?? '';

                $this->connections->offsetUnset($connection);
                $this->sendAll("User $name leaves the chat\n", $connection);
            }
        );
    }

    private function addNewMember(string $name, ConnectionInterface $connection): void
    {
        $name = str_replace(["\n", "\r"], "", $name);
        $this->setConnectionData($connection, ['name' => $name]);
        $this->sendAll("User $name joins the chat\n", $connection);
    }

    private function setConnectionData(ConnectionInterface $connection, array $data): void
    {
        $this->connections->offsetSet($connection, $data);
    }

    private function getConnectionData(ConnectionInterface $connection): array
    {
        return $this->connections->offsetGet($connection);
    }

    /**
     * Send data to all connections from the pool except
     * the specified one.
     */
    private function sendAll(string $data, ConnectionInterface $except): void
    {
        foreach ($this->connections as $conn) {
            if ($conn !== $except) {
                $conn->write($data);
            }
        }
    }
}
