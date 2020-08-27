<?php

use React\Socket\ConnectionInterface;

final class ConnectionsPool
{
    private SplObjectStorage $connections;
    private Output $output;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
        $this->output = new Output();
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

                // If it is the first data received, we add a new member,
                // otherwise send a message
                empty($connectionData) ?
                    $this->addNewMember($data, $connection) :
                    $this->sendMessage($connection, $connectionData['name'], $data);
            }
        );

        // When connection closes detach it from the pool
        $connection->on(
            'close',
            function () use ($connection) {
                $data = $this->getConnectionData($connection);
                $name = $data['name'] ?? '';

                $this->connections->offsetUnset($connection);
                $this->sendAll(
                    $this->output->warning("User $name leaves the chat\n"),
                    $connection
                );
            }
        );
    }

    private function sendMessage(
        ConnectionInterface $connection,
        string $name,
        string $message
    ): void {
        // if is a private message
        preg_match('/^@(\w+):\s*(.+)/', $message, $matches);
        if (!$matches) {
            $this->sendAll("$name: $message", $connection);
            return;
        }

        $sendTo = $matches[1];
        $wasSent = $this->sendTo($sendTo, $name . ': ' . $matches[2]);
        if (!$wasSent) {
            $connection->write($this->output->warning("User $sendTo not found!"));
        }
    }

    private function sendTo(string $name, string $message): bool
    {
        $connection = $this->getConnectionByName($name);
        if (!$connection) {
            return false;
        }

        $connection->write($this->output->message($message) . "\n");
        return true;
    }

    private function addNewMember(string $name, ConnectionInterface $connection): void
    {
        $name = str_replace(["\n", "\r"], "", $name);

        if ($this->getConnectionByName($name)) {
            $connection->write($this->output->warning("Name $name is already taken!") . "\n");
            $connection->write("Enter your name: ");
            return;
        }

        $this->setConnectionData($connection, ['name' => $name]);
        $this->sendAll($this->output->info("User $name joins the chat") . "\n", $connection);
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

    private function getConnectionByName(string $name): ?ConnectionInterface
    {
        /** @var ConnectionInterface $connection */
        foreach ($this->connections as $connection) {
            $data = $this->connections->offsetGet($connection);
            $takenName = $data['name'] ?? '';
            if ($takenName === $name) {
                return $connection;
            }
        }

        return null;
    }
}
