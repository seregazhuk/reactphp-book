<?php

namespace tests\Waiting;

use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use Clue\React\Block;
use React\Promise\PromiseInterface;
use React\Promise\Timer\TimeoutException;

class PromiseFulfillsWithTest extends TestCase
{
    const DEFAULT_TIMEOUT = 2;
    /**
     * @var LoopInterface
     */
    protected $loop;

    protected function setUp()
    {
        $this->loop = Factory::create();
        parent::setUp();
    }

    /** @test */
    public function a_promise_fulfills_with_a_value()
    {
        $deferred = new Deferred();
        $deferred->resolve('foo');

        $this->assertPromiseFulfillsWith($deferred->promise(), 'bar');
    }

    /**
     * @param PromiseInterface $promise
     * @param mixed $expectedValue
     * @param int|null $timeout seconds to wait for resolving
     * @return mixed
     */
    public function assertPromiseFulfillsWith(PromiseInterface $promise, $expectedValue, $timeout = null)
    {
        $failMessage = 'Failed asserting that promise fulfills with a specified value. ';
        try {
            $fulfilledValue = Block\await($promise, $this->loop, $timeout ? : self::DEFAULT_TIMEOUT);
        } catch (TimeoutException $exception) {
            $this->fail($failMessage . 'Promise was rejected by timeout.');
        } catch (Exception $exception) {
            $this->fail($failMessage . 'Promise was rejected.');
        }

        $this->assertEquals($expectedValue, $fulfilledValue, $failMessage);
    }
}

