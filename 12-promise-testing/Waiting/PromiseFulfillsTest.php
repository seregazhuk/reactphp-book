<?php

namespace tests\Waiting;

use Clue\React\Block;
use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Promise\Timer\TimeoutException;

final class PromiseFulfillsTest extends TestCase
{
    private const DEFAULT_TIMEOUT = 2;

    private LoopInterface $loop;

    protected function setUp(): void
    {
        $this->loop = Factory::create();
        parent::setUp();
    }

    /** @test */
    public function a_promise_fulfills(): void
    {
        $deferred = new Deferred();
        $deferred->resolve();

        $this->assertPromiseFulfills($deferred->promise());
    }


    public function assertPromiseFulfills(PromiseInterface $promise, int $timeout = null)
    {
        $failMessage = 'Failed asserting that promise fulfills. ';
        try {
            Block\await($promise, $this->loop, $timeout ? : self::DEFAULT_TIMEOUT);
        } catch (TimeoutException $exception) {
            $this->fail($failMessage . 'Promise was rejected by timeout.');
        } catch (Exception $exception) {
            $this->fail($failMessage . 'Promise was rejected.');
        }
        $this->addToAssertionCount(1);
    }
}

