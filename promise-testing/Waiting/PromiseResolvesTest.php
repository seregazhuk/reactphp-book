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

class PromiseResolvesTest extends TestCase
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
    public function a_promise_resolves()
    {
        $deferred = new Deferred();
        $deferred->resolve();

        $this->assertPromiseResolves($deferred->promise());
    }


    /**
     * @param PromiseInterface $promise
     * @param int|null $timeout seconds to wait for resolving
     * @return mixed
     */
    public function assertPromiseResolves(PromiseInterface $promise, $timeout = null)
    {
        $failMessage = 'Failed asserting that promise resolves. ';
        try {
            $this->addToAssertionCount(1);
            Block\await($promise, $this->loop, $timeout ? : self::DEFAULT_TIMEOUT);
        } catch (TimeoutException $exception) {
            $this->fail($failMessage . 'Promise was rejected by timeout.');
        } catch (Exception $exception) {
            $this->fail($failMessage . 'Promise was rejected.');
        }
    }
}

