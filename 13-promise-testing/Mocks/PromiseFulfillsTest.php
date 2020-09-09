<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class PromiseFulfillsTest extends TestCase
{
    /** @test */
    public function a_promise_fulfills(): void
    {
        $deferred = new Deferred();
        $deferred->resolve();

        $this->assertPromiseFulfills($deferred->promise());
    }

    /**
     * @param PromiseInterface $promise
     */
    public function assertPromiseFulfills(PromiseInterface $promise): void
    {
        $promise->then($this->assertCallableCalledOnce());
    }

    public function assertCallableCalledOnce(): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects(self::once())->method('__invoke');

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
