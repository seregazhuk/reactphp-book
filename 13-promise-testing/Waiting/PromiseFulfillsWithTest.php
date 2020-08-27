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

final class PromiseFulfillsWithTest extends TestCase
{
    private const DEFAULT_TIMEOUT = 2;

    private LoopInterface $loop;

    protected function setUp(): void
    {
        $this->loop = Factory::create();
        parent::setUp();
    }

    /** @test */
    public function a_promise_fulfills_with_a_value(): void
    {
        $deferred = new Deferred();
        $deferred->resolve('foo');

        $this->assertPromiseFulfillsWith($deferred->promise(), 'foo');
    }

    public function assertPromiseFulfillsWith(
        PromiseInterface $promise,
        $expectedValue,
        int $timeout = null
    ): void {
        $failMessage = 'Failed asserting that promise fulfills with a specified value. ';
        try {
            $fulfilledValue = Block\await($promise, $this->loop, $timeout ?: self::DEFAULT_TIMEOUT);
        } catch (TimeoutException $exception) {
            $this->fail($failMessage . 'Promise was rejected by timeout.');
        } catch (Exception $exception) {
            $this->fail($failMessage . 'Promise was rejected.');
        }

        $this->assertEquals($expectedValue, $fulfilledValue, $failMessage);
    }
}

