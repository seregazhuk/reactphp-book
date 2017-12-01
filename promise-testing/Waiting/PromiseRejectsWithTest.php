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

class PromiseRejectsWithTest extends TestCase
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
    public function a_promise_rejects_with_a_reason()
    {
        $deferred = new Deferred();
        $deferred->reject(new \LogicException());

        $this->assertPromiseRejectsWith($deferred->promise(), \InvalidArgumentException::class);
    }

    /**
     * @param PromiseInterface $promise
     * @param string $reasonExceptionClass
     * @param int|null $timeout seconds to wait for resolving
     * @return void
     */
    public function assertPromiseRejectsWith(PromiseInterface $promise, $reasonExceptionClass, $timeout = null)
    {
        $reason = $this->assertPromiseRejects($promise, $timeout);
        $this->assertInstanceOf(
            $reasonExceptionClass,
            $reason,
            'Failed asserting that promise rejects with a specified reason.'
        );
    }

    /**
     * @param PromiseInterface $promise
     * @param int|null $timeout seconds to wait for resolving
     * @return mixed
     */
    public function assertPromiseRejects(PromiseInterface $promise, $timeout = null)
    {
        try {
            $this->addToAssertionCount(1);
            Block\await($promise, $this->loop, $timeout ? : self::DEFAULT_TIMEOUT);
        } catch (Exception $exception) {
            return $exception;
        }
        $this->fail('Failed asserting that promise rejects. Promise was resolved.');
    }
}

