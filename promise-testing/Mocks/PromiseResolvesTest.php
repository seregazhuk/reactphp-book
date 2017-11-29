<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class PromiseResolvesTest extends TestCase
{
    /** @test */
    public function a_promise_resolves()
    {
        $deferred = new Deferred();
        $deferred->reject();

        $this->assertPromiseResolves($deferred->promise());
    }

    /**
     * @param PromiseInterface $promise
     */
    public function assertPromiseResolves(PromiseInterface $promise)
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
