<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class PromiseRejectsWithTest extends TestCase
{
    /** @test */
    public function a_promise_rejects_with()
    {
        $deferred = new Deferred();
        $deferred->reject(new \LogicException());

        $this->assertPromiseRejectsWith($deferred->promise(), \InvalidArgumentException::class);
    }

    /**
     * @param PromiseInterface $promise
     * @param string $exceptionClass
     */
    public function assertPromiseRejectsWith(PromiseInterface $promise, $exceptionClass)
    {
        $promise->then(null, $this->assertCallableCalledOnceWithObjectOf($exceptionClass));
    }

    /**
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject|callable
     */
    public function assertCallableCalledOnceWithObjectOf($className)
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects($this->once())->method('__invoke')->with($this->isInstanceOf($className));

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
