<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

final class PromiseRejectsWithTest extends TestCase
{
    /** @test */
    public function a_promise_rejects_with(): void
    {
        $deferred = new Deferred();
        $deferred->reject(new \LogicException());

        $this->assertPromiseRejectsWith($deferred->promise(), \LogicException::class);
    }

    public function assertPromiseRejectsWith(
        PromiseInterface $promise,
        string $exceptionClass
    ): void {
        $promise->then(null, $this->assertCallableCalledOnceWithObject($exceptionClass));
    }

    public function assertCallableCalledOnceWithObject(string $className): callable
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
