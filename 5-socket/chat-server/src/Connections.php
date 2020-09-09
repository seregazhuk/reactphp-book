<?php

declare(strict_types=1);

namespace Chat;

use IteratorAggregate;
use React\Socket\ConnectionInterface;
use SplObjectStorage;

final class Connections implements IteratorAggregate
{
    private SplObjectStorage $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    public function add(ConnectionInterface $connection, string $name): void
    {
        $this->connections->offsetSet($connection, $name);
    }

    public function hasWithUsername(string $name): bool
    {
        return $this->getByUserName($name) !== null;
    }

    public function getByUsername(string $name): ?ConnectionInterface
    {
        /** @var ConnectionInterface $connection */
        foreach ($this->connections as $connection) {
            $takenName = $this->connections->offsetGet($connection);
            if ($takenName === $name) {
                return $connection;
            }
        }

        return null;
    }

    public function getUsername(ConnectionInterface $connection): string
    {
        return $this->connections->offsetGet($connection);
    }

    public function exists(ConnectionInterface $connection): bool
    {
        return $this->connections->offsetExists($connection);
    }

    public function remove(ConnectionInterface $connection): void
    {
        $this->connections->detach($connection);
    }

    public function getIterator()
    {
        return $this->connections;
    }
}
