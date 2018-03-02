<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class PromiseFulfillsTest extends TestCase
{
    /** @test */
    public function a_promise_fulfills()
    {
        $deferred = new Deferred();
        $deferred->reject();

        $this->assertPromiseFulfills($deferred->promise());
    }

    /**
     * @param PromiseInterface $promise
     */
    public function assertPromiseFulfills(PromiseInterface $promise)
    {
        $promise->then($this->assertCallableCalledOnce());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|callable
     */
    public function assertCallableCalledOnce()
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects($this->once())->method('__invoke');

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
