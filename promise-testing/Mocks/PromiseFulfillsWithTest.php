<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class PromiseFulfillsWithTest extends TestCase
{
    /** @test */
    public function a_promise_fulfills()
    {
        $deferred = new Deferred();
        $deferred->resolve('foo');

        $this->assertPromiseFulfillsWith($deferred->promise(), 'bar');
    }

    /**
     * @param PromiseInterface $promise
     * @param mixed $value
     */
    public function assertPromiseFulfillsWith(PromiseInterface $promise, $value)
    {
        $promise->then($this->assertCallableCalledOnceWith($value));
    }

    /**
     * @param mixed $value
     * @return PHPUnit_Framework_MockObject_MockObject|callable
     */
    public function assertCallableCalledOnceWith($value)
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects($this->once())->method('__invoke')->with($value);

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
